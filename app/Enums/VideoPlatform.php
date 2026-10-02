<?php

namespace App\Enums;

enum VideoPlatform: string
{
    case YouTubeShorts = 'youtube_shorts';
    case YouTube = 'youtube';
    case Vimeo = 'vimeo';
    case Instagram = 'instagram';

    public function label(): string
    {
        return match ($this) {
            self::YouTubeShorts => 'YouTube Shorts (9:16 Vertical)',
            self::YouTube => 'YouTube Standard',
            self::Vimeo => 'Vimeo',
            self::Instagram => 'Instagram Reel',
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
