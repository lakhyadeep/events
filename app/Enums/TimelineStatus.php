<?php

namespace App\Enums;

enum TimelineStatus: string
{
    case Completed = 'completed';
    case Ongoing = 'ongoing';
    case Upcoming = 'upcoming';

    public function label(): string
    {
        return match ($this) {
            self::Completed => 'Completed (Green Indicator)',
            self::Ongoing => 'Ongoing / Current Phase (Gold Pulse)',
            self::Upcoming => 'Upcoming (Gray Scheduled)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Completed => 'success',
            self::Ongoing => 'warning',
            self::Upcoming => 'gray',
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
