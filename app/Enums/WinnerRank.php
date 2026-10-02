<?php

namespace App\Enums;

enum WinnerRank: string
{
    case First = '1st';
    case Second = '2nd';
    case Third = '3rd';
    case SpecialMention = 'Special Mention';
    case PeopleChoice = 'People Choice';

    public function label(): string
    {
        return match ($this) {
            self::First => '1st Prize (Champion / Gold Trophy)',
            self::Second => '2nd Prize (Runner Up / Silver Trophy)',
            self::Third => '3rd Prize (Bronze Trophy)',
            self::SpecialMention => 'Special Jury Mention',
            self::PeopleChoice => "People's Choice Award",
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
