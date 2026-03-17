<?php

namespace App\Domain\Calculator\Enums;

enum Language: string
{
    case English = 'angol';
    case French = 'francia';
    case German = 'nemet';
    case Italian = 'olasz';
    case Russian = 'orosz';
    case Spanish = 'spanyol';

    public function label(): string
    {
        return match ($this) {
            Language::English => 'Angol',
            Language::French => 'Francia',
            Language::German => 'Német',
            Language::Italian => 'Olasz',
            Language::Russian => 'Orosz',
            Language::Spanish => 'Spanyol',
        };
    }
}
