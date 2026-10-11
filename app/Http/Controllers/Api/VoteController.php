<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Vote;
use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'award_id' => ['nullable', 'integer', 'exists:awards,id'],
        ]);

        /** @var Event $event */
        $event = Event::findOrFail($validated['event_id']);

        if (! $event->is_voting_active) {
            return response()->json([
                'success' => false,
                'allowed' => false,
                'message' => 'Voting is currently closed for this event.',
            ], 422);
        }

        $awardId = ! empty($validated['award_id']) ? (int) $validated['award_id'] : null;
        $check = $event->canMobileVote($validated['phone'], $awardId);

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

        if (! $event->is_voting_active) {
            return response()->json([
                'success' => false,
                'message' => 'Voting has officially concluded for this event.',
            ], 422);
        }

        /** @var Participant $participant */
        $participant = $event->participants()->findOrFail($validated['participant_id']);

        $awardId = ! empty($validated['award_id']) ? (int) $validated['award_id'] : null;

        // 1. Double check voting eligibility rules
        $check = $event->canMobileVote($validated['phone'], $awardId);
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

        // 3. Record official Ballot Vote
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']) ?? '';
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $vote = Vote::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'award_id' => $awardId,
            'mobile_number' => $cleanPhone,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $recipientName = $participant->display_name ?: $participant->name;

        return response()->json([
            'success' => true,
            'message' => "Your vote for \"{$recipientName}\" has been recorded successfully!",
            'data' => [
                'vote_id' => $vote->id,
                'participant_name' => $recipientName,
                'voting_mode' => $event->voting_mode->value,
            ],
        ], 200);
    }
}
