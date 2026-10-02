<?php

namespace App\Enums;

enum Zone: string
{
    case NorthKolkata = 'North Kolkata';
    case SouthKolkata = 'South Kolkata';
    case CentralKolkata = 'Central Kolkata';
    case EastKolkata = 'East Kolkata';
    case Howrah = 'Howrah';
    case SaltLakeNewTown = 'Salt Lake & New Town';
    case Other = 'Other';

    public static function options(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
