<?php

namespace App\Enums;

enum BannerCategory: string
{
    case Hero = 'hero';
    case Marketing = 'marketing';

    public function label(): string
    {
        return match ($this) {
            self::Hero => 'Hero Section Banner',
            self::Marketing => 'Marketing Promotional Banner',
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
