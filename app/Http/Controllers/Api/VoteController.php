<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Vote;
use App\Services\AnalyticsService;
use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoteController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    /**
     * Check if a mobile number is eligible to vote before requesting OTP.
     */
    public function checkEligibility(Request $request): JsonResponse
    {
        if ($request->has('award_ids') && is_array($request->input('award_ids')) && count(array_filter($request->input('award_ids'))) > 1) {
            return response()->json([
                'success' => false,
                'allowed' => false,
                'message' => 'Only one award category is allowed per vote.',
            ], 422);
        }

        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'participant_id' => ['nullable', 'integer', 'exists:participants,id'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'award_id' => ['nullable', 'integer', 'exists:awards,id'],
            'award_ids' => ['nullable', 'array', 'max:1'],
            'award_ids.*' => ['integer', 'exists:awards,id'],
        ]);

        /** @var Event $event */
        $event = Event::findOrFail($validated['event_id']);

        if (! $event->isVotingOpen()) {
            return response()->json([
                'success' => false,
                'allowed' => false,
                'message' => $event->getVotingClosedMessage(),
            ], 422);
        }

        $participantId = ! empty($validated['participant_id']) ? (int) $validated['participant_id'] : null;
        $awardId = ! empty($validated['award_id'])
            ? (int) $validated['award_id']
            : (! empty($validated['award_ids']) ? (int) reset($validated['award_ids']) : null);

        if ($awardId) {
            $award = $event->awards()->find($awardId);
            if (! $award || ! $award->is_active || ! $award->is_voting_allowed) {
                return response()->json([
                    'success' => false,
                    'allowed' => false,
                    'message' => 'This award category is not open for public voting.',
                ], 422);
            }

            $check = $event->canMobileVote($validated['phone'], $participantId, $awardId);
            if (! $check['allowed']) {
                return response()->json([
                    'success' => false,
                    'allowed' => false,
                    'message' => $check['message'],
                ], 422);
            }
        } else {
            $check = $event->canMobileVote($validated['phone'], $participantId, null);
            if (! $check['allowed']) {
                return response()->json([
                    'success' => false,
                    'allowed' => false,
                    'message' => $check['message'],
                ], 422);
            }
        }

        return response()->json([
            'success' => true,
            'allowed' => true,
            'message' => 'Mobile number is eligible to cast a vote.',
        ], 200);
    }

    /**
     * Verify OTP and record the verified vote.
     */
    public function cast(Request $request): JsonResponse
    {
        if ($request->has('award_ids') && is_array($request->input('award_ids')) && count(array_filter($request->input('award_ids'))) > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Only one award category is allowed to be voted on.',
            ], 422);
        }

        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'participant_id' => ['required', 'integer', 'exists:participants,id'],
            'award_id' => ['nullable', 'integer', 'exists:awards,id'],
            'award_ids' => ['nullable', 'array', 'max:1'],
            'award_ids.*' => ['integer', 'exists:awards,id'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'otp' => ['required', 'string', 'min:4', 'max:10'],
            'name' => ['nullable', 'string', 'max:255'],
            'voter_name' => ['nullable', 'string', 'max:255'],
        ]);

        /** @var Event $event */
        $event = Event::findOrFail($validated['event_id']);

        if (! $event->isVotingOpen()) {
            return response()->json([
                'success' => false,
                'message' => $event->getVotingClosedMessage(),
            ], 422);
        }

        /** @var Participant $participant */
        $participant = $event->participants()->findOrFail($validated['participant_id']);

        $awardId = ! empty($validated['award_id'])
            ? (int) $validated['award_id']
            : (! empty($validated['award_ids']) ? (int) reset($validated['award_ids']) : null);

        // 1. Verify award allows voting
        if ($awardId) {
            $award = $event->awards()->find($awardId);
            if (! $award || ! $award->is_active || ! $award->is_voting_allowed) {
                return response()->json([
                    'success' => false,
                    'message' => 'This award category is not open for public voting.',
                ], 422);
            }
        }

        // 2. Check voting eligibility rules for target award
        $check = $event->canMobileVote($validated['phone'], $participant->id, $awardId);
        if (! $check['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $check['message'],
            ], 422);
        }

        // 3. Verify OTP code
        $verifyResult = $this->otpService->verifyOtp($validated['phone'], $validated['otp'], 'voting');

        if (! $verifyResult->successful) {
            return response()->json([
                'success' => false,
                'message' => $verifyResult->message,
                'data' => [
                    'attempts_remaining' => $verifyResult->attemptsRemaining,
                ],
            ], 422);
        }

        // 4. Record official Vote atomically
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']) ?? '';
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $voterName = ! empty($validated['voter_name'])
            ? trim($validated['voter_name'])
            : (! empty($validated['name']) ? trim($validated['name']) : null);

        try {
            $vote = DB::transaction(function () use ($event, $participant, $awardId, $cleanPhone, $voterName, $request) {
                $recheck = $event->canMobileVote($cleanPhone, $participant->id, $awardId);
                if (! $recheck['allowed']) {
                    throw new \RuntimeException($recheck['message']);
                }

                return Vote::create([
                    'event_id' => $event->id,
                    'participant_id' => $participant->id,
                    'award_id' => $awardId,
                    'mobile_number' => $cleanPhone,
                    'voter_name' => $voterName,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            // Track vote_verified in analytics
            try {
                $visitorId = $request->cookie('analytics_vid') ?? (string) Str::uuid();
                app(AnalyticsService::class)->recordActivity(
                    actionType: 'vote_verified',
                    visitorId: $visitorId,
                    sessionId: $request->hasSession() ? $request->session()->getId() : null,
                    eventId: $event->id,
                    targetType: 'participant',
                    targetId: $participant->id,
                    metadata: [
                        'award_id' => $awardId,
                        'vote_id' => $vote->id,
                    ],
                    url: $request->header('referer'),
                    deviceType: app(AnalyticsService::class)->detectDeviceType($request->userAgent())
                );
            } catch (\Throwable) {
                // Tracking failure must never interrupt voting confirmation
            }
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $recipientName = $participant->display_name ?: $participant->name;

        return response()->json([
            'success' => true,
            'message' => "Your vote for \"{$recipientName}\" has been recorded successfully!",
            'data' => [
                'vote_id' => $vote->id,
                'vote_ids' => [$vote->id],
                'votes_count' => 1,
                'participant_name' => $recipientName,
                'voter_name' => $vote->voter_name,
                'award_id' => $awardId,
                'award_ids' => array_values(array_filter([$awardId])),
            ],
        ], 200);
    }
}
