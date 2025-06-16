<?php

namespace App\Enum;

enum ShiftEnum: string
{
    case DAY = "day";
    case NIGHT = "night";

    public function description(): string
    {
        return match ($this) {
            self::DAY => "Day",
            self::NIGHT => "Night",
        };
    }
}
