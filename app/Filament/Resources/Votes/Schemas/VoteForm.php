<?php

namespace App\Filament\Resources\Votes\Schemas;

use App\Models\Vote;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class VoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make([
                    Section::make('Vote Details')
                        ->description('Contestant and category selected by the voter.')
                        ->schema([
                            Select::make('event_id')
                                ->label('Festival Event')
                                ->relationship('event', 'display_name')
                                ->disabled(),
                            Select::make('participant_id')
                                ->label('Puja Committee / Candidate')
                                ->relationship('participant', 'display_name')
                                ->disabled(),
                            Select::make('award_id')
                                ->label('Award Category')
                                ->relationship('award', 'display_name')
                                ->placeholder('Overall / General Event Vote')
                                ->disabled(),
                        ]),

                    Section::make('OTP Delivery & Verification Trail')
                        ->description('Cryptographic SMS delivery and OTP verification audit log.')
                        ->columns(3)
                        ->schema([
                            Placeholder::make('otp_status')
                                ->label('OTP Status')
                                ->content(function (?Vote $record): HtmlString {
                                    if (! $record) {
                                        return new HtmlString('<span class="text-gray-400">N/A</span>');
                                    }
                                    $trail = $record->otp_audit_trail;
                                    if (! $trail['has_otp']) {
                                        return new HtmlString('<span style="background-color: rgba(156, 163, 175, 0.2); color: #6b7280; padding: 3px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">● No OTP Record</span>');
                                    }

                                    $color = $trail['status_color'];
                                    $label = $trail['status'];
                                    $badgeStyle = match ($color) {
                                        'success' => 'background-color: #d1fae5; color: #065f46;',
                                        'danger' => 'background-color: #fee2e2; color: #991b1b;',
                                        default => 'background-color: #fef3c7; color: #92400e;',
                                    };

                                    return new HtmlString("<span style=\"{$badgeStyle}; padding: 3px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;\">● {$label}</span>");
                                }),

                            Placeholder::make('otp_dispatched_at')
                                ->label('SMS Dispatched At')
                                ->content(fn (?Vote $record): string => $record?->otp_audit_trail['dispatched_at'] ?? 'N/A'),

                            Placeholder::make('otp_verified_at')
                                ->label('OTP Verified At')
                                ->content(function (?Vote $record): string {
                                    $trail = $record?->otp_audit_trail;
                                    if (! $trail || ! $trail['verified_at']) {
                                        return 'Unverified / Incomplete';
                                    }
                                    $latency = $trail['verification_latency'] ? " ({$trail['verification_latency']})" : '';

                                    return $trail['verified_at'].$latency;
                                }),

                            Placeholder::make('otp_attempts')
                                ->label('Failed Attempts')
                                ->content(function (?Vote $record): string {
                                    $attempts = $record?->otp_audit_trail['attempts'] ?? 0;

                                    return $attempts === 0 ? '0 (Verified first attempt)' : "{$attempts} failed attempts recorded";
                                }),

                            Placeholder::make('otp_ip')
                                ->label('OTP Request IP')
                                ->content(function (?Vote $record): HtmlString {
                                    $trail = $record?->otp_audit_trail;
                                    $otpIp = $trail['otp_ip'] ?? 'Unknown';
                                    $voteIp = $record?->ip_address ?? 'Unknown';

                                    if (! ($trail['has_otp'] ?? false)) {
                                        return new HtmlString("<span style=\"color: #6b7280;\">{$otpIp}</span>");
                                    }

                                    if ($trail['ip_matches_vote']) {
                                        return new HtmlString("<span style=\"color: #059669; font-weight: 600;\">{$otpIp}</span> <span style=\"font-size: 0.75rem; color: #059669;\">(✓ Matches Vote IP)</span>");
                                    }

                                    return new HtmlString("<span style=\"color: #dc2626; font-weight: 700;\">{$otpIp}</span> <span style=\"font-size: 0.75rem; color: #dc2626;\">(⚠️ Differs from Vote IP: {$voteIp})</span>");
                                }),

                            Placeholder::make('total_otps')
                                ->label('Total OTPs Sent')
                                ->content(function (?Vote $record): string {
                                    $count = $record?->otp_audit_trail['total_dispatched'] ?? 0;

                                    return "{$count} message(s) dispatched to number";
                                }),

                            Placeholder::make('otp_history')
                                ->label('Recent OTP Dispatch Trail')
                                ->columnSpanFull()
                                ->content(function (?Vote $record): HtmlString {
                                    if (! $record) {
                                        return new HtmlString('');
                                    }
                                    $otps = $record->otp_audit_trail['recent_otps'];
                                    if ($otps->isEmpty()) {
                                        return new HtmlString('<span style="color: #6b7280; font-size: 0.875rem;">No historical OTP records found for this mobile number.</span>');
                                    }

                                    $rows = '';
                                    foreach ($otps as $idx => $otp) {
                                        $num = $idx + 1;
                                        $created = $otp->created_at?->timezone('Asia/Kolkata')->format('d M Y, h:i:s A') ?? 'N/A';
                                        $verified = $otp->verified_at?->timezone('Asia/Kolkata')->format('d M Y, h:i:s A') ?? '<span style="color: #9ca3af;">—</span>';
                                        $expires = $otp->expires_at?->timezone('Asia/Kolkata')->format('h:i:s A') ?? 'N/A';
                                        $status = $otp->isVerified() ? '<span style="color: #059669; font-weight: 600;">Verified</span>' : ($otp->isExpired() ? '<span style="color: #dc2626; font-weight: 600;">Expired</span>' : '<span style="color: #d97706; font-weight: 600;">Pending</span>');
                                        $ip = $otp->ip_address ?: 'Unknown';
                                        $attempts = $otp->attempts;

                                        $rows .= "<tr style=\"border-bottom: 1px solid rgba(156, 163, 175, 0.2);\">
                                            <td style=\"padding: 6px 10px;\">#{$num}</td>
                                            <td style=\"padding: 6px 10px;\">{$created}</td>
                                            <td style=\"padding: 6px 10px;\">{$status}</td>
                                            <td style=\"padding: 6px 10px;\">{$verified}</td>
                                            <td style=\"padding: 6px 10px;\">{$expires}</td>
                                            <td style=\"padding: 6px 10px;\">{$attempts}</td>
                                            <td style=\"padding: 6px 10px; font-family: monospace;\">{$ip}</td>
                                        </tr>";
                                    }

                                    return new HtmlString("
                                        <div style=\"overflow-x: auto; width: 100%; border: 1px solid rgba(156, 163, 175, 0.2); border-radius: 6px; margin-top: 4px;\">
                                            <table style=\"width: 100%; text-align: left; font-size: 0.8rem; border-collapse: collapse;\">
                                                <thead>
                                                    <tr style=\"background-color: rgba(156, 163, 175, 0.1); border-bottom: 1px solid rgba(156, 163, 175, 0.2); font-weight: 600;\">
                                                        <th style=\"padding: 6px 10px;\">#</th>
                                                        <th style=\"padding: 6px 10px;\">Requested At</th>
                                                        <th style=\"padding: 6px 10px;\">Status</th>
                                                        <th style=\"padding: 6px 10px;\">Verified At</th>
                                                        <th style=\"padding: 6px 10px;\">Expires</th>
                                                        <th style=\"padding: 6px 10px;\">Attempts</th>
                                                        <th style=\"padding: 6px 10px;\">Request IP</th>
                                                    </tr>
                                                </thead>
                                                <tbody>{$rows}</tbody>
                                            </table>
                                        </div>
                                    ");
                                }),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make([
                    Section::make('Voter Identity & Audit')
                        ->description('Identity and device audit recorded during vote submission.')
                        ->schema([
                            TextInput::make('voter_name')
                                ->label('Voter Name')
                                ->placeholder('Anonymous / Not provided')
                                ->disabled(),
                            TextInput::make('mobile_number')
                                ->label('Verified Mobile Number')
                                ->disabled(),
                            TextInput::make('device_name')
                                ->label('Detected Device')
                                ->disabled(),
                            TextInput::make('ip_address')
                                ->label('Vote Submission IP')
                                ->disabled()
                                ->helperText(function (?Vote $record): ?string {
                                    if (! $record || ! $record->ip_address) {
                                        return null;
                                    }
                                    $count = $record->same_ip_votes_count;
                                    if ($count > 1) {
                                        return "⚠️ Same IP Cluster: {$count} total votes recorded from this IP address.";
                                    }

                                    return '✓ Unique IP: only 1 vote recorded from this IP address.';
                                }),
                            Textarea::make('user_agent')
                                ->label('Raw User Agent')
                                ->rows(3)
                                ->disabled(),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }
}
