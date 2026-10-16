<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ManageOtpSettings;
use App\Models\OtpSetting;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class SmsBalanceAlertWidget extends Widget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.sms-balance-alert-widget';

    public static function canView(): bool
    {
        $setting = OtpSetting::current();

        if ($setting->active_driver !== 'fast2sms') {
            return false;
        }

        return $setting->isFast2SmsBalanceLow();
    }

    /**
     * @return array{
     *     driver: string,
     *     configured: bool,
     *     successful: bool,
     *     balance: float|null,
     *     threshold: float,
     *     is_low: bool,
     *     message: string,
     *     checked_at: string|null,
     * }
     */
    public function getBalanceDetailsProperty(): array
    {
        return OtpSetting::current()->getFast2SmsBalanceDetails();
    }

    public function getSettingsUrlProperty(): string
    {
        return ManageOtpSettings::getUrl();
    }

    public function refreshBalance(): void
    {
        $details = OtpSetting::current()->getFast2SmsBalanceDetails(forceRefresh: true);

        if (! $details['successful']) {
            Notification::make()
                ->title('Fast2SMS Connection Check')
                ->body($details['message'] ?? 'Unable to fetch Fast2SMS balance.')
                ->warning()
                ->send();
        } elseif ($details['is_low']) {
            $bal = number_format((float) $details['balance'], 2);
            Notification::make()
                ->title('⚠️ Fast2SMS Balance Still Low')
                ->body("Current Balance: ₹{$bal} (Threshold: ₹{$details['threshold']}).")
                ->danger()
                ->send();
        } else {
            $bal = number_format((float) $details['balance'], 2);
            Notification::make()
                ->title('Fast2SMS Wallet Healthy')
                ->body("Balance updated: ₹{$bal}.")
                ->success()
                ->send();
        }
    }
}
