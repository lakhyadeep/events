<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    /**
     * Dispatch an OTP to a designated mobile number.
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'purpose' => ['nullable', 'string', 'max:50'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'participant_id' => ['nullable', 'integer', 'exists:participants,id'],
            'award_id' => ['nullable', 'integer', 'exists:awards,id'],
        ]);

        $purpose = $validated['purpose'] ?? 'verification';

        // Pre-flight check for voting to avoid wasting SMS credits if user already voted
        if ($purpose === 'voting' && ! empty($validated['event_id'])) {
            $event = Event::find($validated['event_id']);
            if ($event) {
                if (! $event->isVotingOpen()) {
                    return response()->json([
                        'success' => false,
                        'message' => $event->getVotingClosedMessage(),
                    ], 422);
                }

                $participantId = ! empty($validated['participant_id']) ? (int) $validated['participant_id'] : null;
                $awardId = ! empty($validated['award_id']) ? (int) $validated['award_id'] : null;
                $check = $event->canMobileVote($validated['phone'], $participantId, $awardId);
                if (! $check['allowed']) {
                    return response()->json([
                        'success' => false,
                        'message' => $check['message'],
                    ], 422);
                }
            }
        }

        $result = $this->otpService->sendOtp($validated['phone'], $purpose, $request->ip());

        if (! $result->successful) {
            return response()->json([
                'success' => false,
                'message' => $result->message,
                'data' => [
                    'resend_after_seconds' => $result->resendAfterSeconds,
                ],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $result->message,
            'data' => [
                'resend_after_seconds' => $result->resendAfterSeconds,
                'expires_in_seconds' => $result->expiresInSeconds,
            ],
        ], 200);
    }

    /**
     * Validate an OTP code provided by the client.
     */
    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'code' => ['required', 'string', 'min:4', 'max:10'],
            'purpose' => ['nullable', 'string', 'max:50'],
        ]);

        $purpose = $validated['purpose'] ?? 'verification';
        $result = $this->otpService->verifyOtp($validated['phone'], $validated['code'], $purpose);

        if (! $result->successful) {
            return response()->json([
                'success' => false,
                'message' => $result->message,
                'data' => [
                    'attempts_remaining' => $result->attemptsRemaining,
                ],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $result->message,
        ], 200);
    }
}
