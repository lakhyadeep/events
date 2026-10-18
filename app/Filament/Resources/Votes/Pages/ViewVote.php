<?php

namespace App\Filament\Resources\Votes\Pages;

use App\Filament\Resources\Votes\VoteResource;
use App\Models\Vote;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Cache;

class ViewVote extends ViewRecord
{
    protected static string $resource = VoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('blacklist')
                ->label('Blacklist Number')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Blacklist Mobile Number')
                ->modalDescription('Block this number from ever receiving OTPs or submitting votes in the future.')
                ->action(function (Vote $record): void {
                    $cached = (array) Cache::get('fraud_disposable_blacklist', []);
                    if (! in_array($record->mobile_number, $cached, true)) {
                        $cached[] = $record->mobile_number;
                        Cache::put('fraud_disposable_blacklist', $cached, now()->addYears(1));
                    }

                    Notification::make()
                        ->title('Number Blacklisted')
                        ->body("Mobile number +91 {$record->mobile_number} has been blocked from voting.")
                        ->danger()
                        ->send();
                }),

            DeleteAction::make()
                ->label('Void Vote')
                ->modalHeading('Void Vote')
                ->modalDescription('Are you sure you want to void this vote? This action will permanently remove the vote from the official tally.'),
        ];
    }
}
