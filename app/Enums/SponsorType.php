<?php

namespace App\Enums;

enum SponsorType: string
{
    case PresentingPartner = 'presenting_partner';
    case AssociateSponsor = 'associate_sponsor';
    case PoweredBy = 'powered_by';
    case CoSponsor = 'co_sponsor';
    case Partner = 'partner';

    public function label(): string
    {
        return match ($this) {
            self::PresentingPartner => 'Presenting Partner (Masthead Title Slot)',
            self::AssociateSponsor => 'Associate Sponsor (Masthead Slots 1–5)',
            self::PoweredBy => 'Powered By Partner',
            self::CoSponsor => 'Co-Sponsor',
            self::Partner => 'Official Partner',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
