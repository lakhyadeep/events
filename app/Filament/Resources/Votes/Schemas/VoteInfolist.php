<?php

namespace App\Filament\Resources\Votes\Schemas;

use App\Models\Vote;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VoteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    // Section 1: Contest & Ballot Details
                    Section::make('Contest & Ballot Details')
                        ->icon('heroicon-o-ticket')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('participant.display_name')
                                ->label('Voted For (Puja / Candidate)')
                                ->weight('bold')
                                ->size('lg')
                                ->color('primary')
                                ->icon('heroicon-o-user-group'),

                            TextEntry::make('award.display_name')
                                ->label('Award Category')
                                ->badge()
                                ->color('warning')
                                ->placeholder('Overall People\'s Choice')
                                ->icon('heroicon-o-trophy'),

                            TextEntry::make('event.display_name')
                                ->label('Festival Event')
                                ->icon('heroicon-o-calendar')
                                ->weight('medium'),
                        ]),

                    // Section 2: Cryptographic OTP Audit Trail
                    Section::make('Cryptographic OTP Verification Trail')
                        ->icon('heroicon-o-shield-check')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('otp_status')
                                ->label('OTP Status')
                                ->state(fn (Vote $record): string => $record->otp_audit_trail['status'])
                                ->badge()
                                ->color(fn (Vote $record): string => $record->otp_audit_trail['status_color'])
                                ->icon(fn (Vote $record): string => $record->otp_audit_trail['status_color'] === 'success' ? 'heroicon-o-check-badge' : 'heroicon-o-exclamation-circle'),

                            TextEntry::make('otp_dispatched')
                                ->label('SMS Dispatched At')
                                ->state(fn (Vote $record): string => $record->otp_audit_trail['dispatched_at'] ?? 'No Record')
                                ->icon('heroicon-o-paper-airplane'),

                            TextEntry::make('otp_verified')
                                ->label('OTP Verified At')
                                ->state(function (Vote $record): string {
                                    $trail = $record->otp_audit_trail;
                                    if (! $trail['verified_at']) {
                                        return 'Unverified / Incomplete';
                                    }

                                    return $trail['verified_at'].($trail['verification_latency'] ? " ({$trail['verification_latency']})" : '');
                                })
                                ->icon('heroicon-o-check-circle'),

                            TextEntry::make('otp_attempts')
                                ->label('Failed Attempts')
                                ->state(function (Vote $record): string {
                                    $attempts = $record->otp_audit_trail['attempts'] ?? 0;

                                    return $attempts === 0 ? '0 (Verified on 1st attempt)' : "{$attempts} failed attempts";
                                })
                                ->icon('heroicon-o-key'),

                            TextEntry::make('otp_ip')
                                ->label('OTP Request IP')
                                ->state(fn (Vote $record): string => $record->otp_audit_trail['otp_ip'] ?? 'Unknown')
                                ->fontFamily('mono')
                                ->helperText(function (Vote $record): ?string {
                                    $trail = $record->otp_audit_trail;
                                    if (! $trail['has_otp']) {
                                        return null;
                                    }

                                    return $trail['ip_matches_vote'] ? '✓ Matches vote submission IP' : "⚠️ Differs from vote IP ({$record->ip_address})";
                                })
                                ->color(fn (Vote $record): string => ($record->otp_audit_trail['ip_matches_vote'] ?? false) ? 'success' : 'danger'),

                            TextEntry::make('total_otps')
                                ->label('Total OTPs Sent')
                                ->state(fn (Vote $record): string => "{$record->otp_audit_trail['total_dispatched']} message(s) dispatched to number")
                                ->icon('heroicon-o-chat-bubble-bottom-center-text'),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    // Section 4: Integrity Assessment
                    Section::make('Integrity & Fraud Check')
                        ->icon('heroicon-o-shield-exclamation')
                        ->schema([
                            TextEntry::make('fraud_flag')
                                ->label('Fraud Assessment')
                                ->state(fn (Vote $record): string => $record->fraud_assessment['label'])
                                ->badge()
                                ->color(fn (Vote $record): string => $record->fraud_assessment['color'])
                                ->icon(fn (Vote $record): string => $record->fraud_assessment['is_fraud'] ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                                ->helperText(fn (Vote $record): string => $record->fraud_assessment['reason']),

                            TextEntry::make('created_at')
                                ->label('Cast At')
                                ->dateTime('d M Y, h:i:s A')
                                ->timezone('Asia/Kolkata')
                                ->icon('heroicon-o-clock'),
                        ]),

                    // Section 5: Voter Identity & Device Audit
                    Section::make('Voter Identity & Device Audit')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->schema([
                            TextEntry::make('voter_name')
                                ->label('Voter Name')
                                ->placeholder('Anonymous / Not provided')
                                ->weight('medium')
                                ->icon('heroicon-o-user'),

                            TextEntry::make('mobile_number')
                                ->label('Verified Mobile Number')
                                ->formatStateUsing(function (string $state): string {
                                    $digits = preg_replace('/[^0-9]/', '', $state) ?? '';

                                    return strlen($digits) >= 10 ? '+91 '.substr($digits, 0, 5).'*****' : '+91 '.$state;
                                })
                                ->copyable()
                                ->copyableState(fn (Vote $record): string => '+91 '.$record->mobile_number)
                                ->icon('heroicon-o-phone'),

                            TextEntry::make('device_name')
                                ->label('Detected Device')
                                ->badge()
                                ->color('gray')
                                ->icon('heroicon-o-device-phone-mobile'),

                            TextEntry::make('ip_address')
                                ->label('Vote Submission IP')
                                ->fontFamily('mono')
                                ->copyable()
                                ->icon('heroicon-o-globe-alt')
                                ->helperText(function (Vote $record): string {
                                    $count = $record->same_ip_votes_count;

                                    return $count > 1 ? "⚠️ Cluster: {$count} total votes share this IP" : '✓ Unique IP: only 1 vote from this IP';
                                })
                                ->color(fn (Vote $record): string => $record->same_ip_votes_count > 1 ? 'warning' : 'success'),

                            TextEntry::make('user_agent')
                                ->label('Raw User Agent')
                                ->fontFamily('mono')
                                ->size('xs')
                                ->color('gray')
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
