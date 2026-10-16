<?php

namespace App\Filament\Widgets;

use App\Models\Otp;
use App\Models\OtpSetting;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SmsUsageOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $setting = OtpSetting::current();
        $activeDriver = $setting->active_driver;
        $balanceDetails = $setting->getFast2SmsBalanceDetails();

        $usageData = cache()->remember('filament_sms_usage_widget_stats', now()->addSeconds(30), function (): array {
            $todayCount = Otp::whereDate('created_at', today())->count();
            $monthCount = Otp::where('created_at', '>=', now()->startOfMonth())->count();
            $totalCount = Otp::count();
            $verifiedCount = Otp::whereNotNull('verified_at')->count();
            $rate = $totalCount > 0 ? round(($verifiedCount / $totalCount) * 100, 1) : 0.0;

            // Trend for the last 5 days
            $dailyTrend = [];
            for ($i = 4; $i >= 0; $i--) {
                $dailyTrend[] = Otp::whereDate('created_at', now()->subDays($i)->toDateString())->count();
            }

            return [
                'today' => $todayCount,
                'month' => $monthCount,
                'total' => $totalCount,
                'verified' => $verifiedCount,
                'rate' => $rate,
                'trend' => $dailyTrend,
            ];
        });

        // 1. Gateway Wallet Balance Stat
        if ($activeDriver === 'fast2sms') {
            $rawBalance = $balanceDetails['balance'] ?? null;
            $threshold = number_format((float) ($balanceDetails['threshold'] ?? 50), 2);

            if ($rawBalance !== null) {
                $balanceFormatted = '₹'.number_format((float) $rawBalance, 2);
                $isLow = (bool) ($balanceDetails['is_low'] ?? false);

                $balanceStat = Stat::make('Fast2SMS Wallet Balance', $balanceFormatted)
                    ->description($isLow ? "⚠️ Low balance (Threshold: ₹{$threshold})" : 'Fast2SMS Production Gateway')
                    ->descriptionIcon($isLow ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-wallet')
                    ->color($isLow ? 'danger' : 'success')
                    ->chart([max(0, (int) $rawBalance - 50), (int) $rawBalance]);
            } else {
                $balanceStat = Stat::make('Fast2SMS Wallet Balance', 'Unavailable')
                    ->description($balanceDetails['message'] ?: 'Connection failed or API key missing')
                    ->descriptionIcon('heroicon-m-exclamation-circle')
                    ->color('warning');
            }
        } elseif ($activeDriver === 'log') {
            $balanceStat = Stat::make('SMS Gateway', 'Active (Log Driver)')
                ->description('Local / Sandbox testing (No live costs)')
                ->descriptionIcon('heroicon-m-command-line')
                ->color('gray');
        } else {
            $balanceStat = Stat::make('SMS Gateway', strtoupper($activeDriver))
                ->description('Configured gateway active')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('primary');
        }

        return [
            $balanceStat,

            Stat::make('SMS Dispatched Today', number_format($usageData['today']))
                ->description('OTPs & alerts sent today')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary')
                ->chart(array_map(fn ($val) => max(1, (int) $val), $usageData['trend'])),

            Stat::make('SMS Sent This Month', number_format($usageData['month']))
                ->description("Lifetime: {$usageData['total']} SMS total")
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info')
                ->chart([3, 8, 14, 25, max(1, $usageData['month'])]),

            Stat::make('OTP Verification Rate', "{$usageData['rate']}%")
                ->description("{$usageData['verified']} verified of {$usageData['total']} requested")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($usageData['rate'] >= 60 ? 'success' : ($usageData['rate'] >= 30 ? 'warning' : 'danger'))
                ->chart([20, 40, 60, max(1, (int) $usageData['rate'])]),
        ];
    }
}
