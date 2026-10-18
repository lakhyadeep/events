<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\OtpSetting;
use App\Services\AnalyticsService;
use App\Services\Fraud\FraudPreventionService;
use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otpService,
        protected ?FraudPreventionService $fraudPreventionService = null
    ) {
        $this->fraudPreventionService ??= app(FraudPreventionService::class);
    }

    /**
     * Dispatch an OTP to a designated mobile number.
     */
    public function send(Request $request): JsonResponse
    {
        if ($request->has('award_ids') && is_array($request->input('award_ids')) && count(array_filter($request->input('award_ids'))) > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Only one award category is allowed to be voted on.',
            ], 422);
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'purpose' => ['nullable', 'string', 'max:50'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'participant_id' => ['nullable', 'integer', 'exists:participants,id'],
            'award_id' => ['nullable', 'integer', 'exists:awards,id'],
            'award_ids' => ['nullable', 'array', 'max:1'],
            'award_ids.*' => ['integer', 'exists:awards,id'],
        ]);

        $purpose = $validated['purpose'] ?? 'verification';

        $fraudCheck = $this->fraudPreventionService->validateMobileNumber($validated['phone']);
        if (! $fraudCheck['valid']) {
            return response()->json([
                'success' => false,
                'message' => $fraudCheck['message'] ?? 'Please provide a valid 10-digit Indian mobile number.',
            ], 422);
        }
        $cleanPhone = $fraudCheck['phone'];

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

                $visitorId = $request->header('X-Visitor-Id')
                    ?? $request->input('visitor_id')
                    ?? $request->cookie('analytics_vid')
                    ?? ($request->hasSession() ? $request->session()->getId() : null);
                $deviceCheck = $this->fraudPreventionService->checkDevicePhoneLimit($visitorId, (int) $validated['event_id'], $cleanPhone);
                if (! $deviceCheck['allowed']) {
                    return response()->json([
                        'success' => false,
                        'message' => $deviceCheck['message'],
                    ], 422);
                }

                $ipCheck = $this->fraudPreventionService->checkIpVoteLimit($request->ip(), (int) $validated['event_id']);
                if (! $ipCheck['allowed']) {
                    return response()->json([
                        'success' => false,
                        'message' => $ipCheck['message'],
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
                            'message' => 'This award category is not open for public voting.',
                        ], 422);
                    }

                    $check = $event->canMobileVote($cleanPhone, $participantId, $awardId);
                    if (! $check['allowed']) {
                        return response()->json([
                            'success' => false,
                            'message' => $check['message'],
                        ], 422);
                    }
                } else {
                    $check = $event->canMobileVote($cleanPhone, $participantId, null);
                    if (! $check['allowed']) {
                        return response()->json([
                            'success' => false,
                            'message' => $check['message'],
                        ], 422);
                    }
                }
            }
        }

        $result = $this->otpService->sendOtp($cleanPhone, $purpose, $request->ip());

        if (! $result->successful) {
            return response()->json([
                'success' => false,
                'message' => $result->message,
                'data' => [
                    'resend_after_seconds' => $result->resendAfterSeconds,
                ],
            ], 422);
        }

        $setting = OtpSetting::current();

        if ($purpose === 'voting') {
            try {
                $visitorId = $request->cookie('analytics_vid') ?? (string) Str::uuid();
                $awardId = ! empty($validated['award_id'])
                    ? (int) $validated['award_id']
                    : (! empty($validated['award_ids']) ? (int) reset($validated['award_ids']) : null);

                app(AnalyticsService::class)->recordActivity(
                    actionType: 'vote_initiated',
                    visitorId: $visitorId,
                    sessionId: $request->hasSession() ? $request->session()->getId() : null,
                    eventId: ! empty($validated['event_id']) ? (int) $validated['event_id'] : null,
                    targetType: 'participant',
                    targetId: ! empty($validated['participant_id']) ? (int) $validated['participant_id'] : null,
                    metadata: [
                        'award_id' => $awardId,
                        'phone_masked' => substr($validated['phone'], 0, 2).'******'.substr($validated['phone'], -2),
                    ],
                    url: $request->header('referer'),
                    deviceType: app(AnalyticsService::class)->detectDeviceType($request->userAgent())
                );
            } catch (\Throwable) {
                // Tracking failure must never interrupt user flow
            }
        }

        return response()->json([
            'success' => true,
            'message' => $result->message,
            'data' => [
                'resend_after_seconds' => $result->resendAfterSeconds,
                'expires_in_seconds' => $result->expiresInSeconds,
                'otp_length' => $setting->otp_length,
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
        $cleanPhone = $this->fraudPreventionService->normalizeIndianMobile($validated['phone']) ?? $validated['phone'];
        $result = $this->otpService->verifyOtp($cleanPhone, $validated['code'], $purpose);

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
