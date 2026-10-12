<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Vote;
use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'participant_id' => ['nullable', 'integer', 'exists:participants,id'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'award_id' => ['nullable', 'integer', 'exists:awards,id'],
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
        $awardId = ! empty($validated['award_id']) ? (int) $validated['award_id'] : null;
        $check = $event->canMobileVote($validated['phone'], $participantId, $awardId);

        if (! $check['allowed']) {
            return response()->json([
                'success' => false,
                'allowed' => false,
                'message' => $check['message'],
            ], 422);
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
        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'participant_id' => ['required', 'integer', 'exists:participants,id'],
            'award_id' => ['nullable', 'integer', 'exists:awards,id'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'otp' => ['required', 'string', 'min:4', 'max:10'],
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

        $awardId = ! empty($validated['award_id']) ? (int) $validated['award_id'] : null;

        // 1. Double check voting eligibility rules
        $check = $event->canMobileVote($validated['phone'], $participant->id, $awardId);
        if (! $check['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $check['message'],
            ], 422);
        }

        // 2. Verify OTP code
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

        // 3. Record official Vote atomically
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']) ?? '';
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        try {
            $vote = DB::transaction(function () use ($event, $participant, $awardId, $cleanPhone, $request) {
                $recheck = $event->canMobileVote($cleanPhone, $participant->id, $awardId);
                if (! $recheck['allowed']) {
                    throw new \RuntimeException($recheck['message']);
                }

                return Vote::create([
                    'event_id' => $event->id,
                    'participant_id' => $participant->id,
                    'award_id' => $awardId,
                    'mobile_number' => $cleanPhone,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });
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
                'participant_name' => $recipientName,
                'award_id' => $awardId,
            ],
        ], 200);
    }
}
