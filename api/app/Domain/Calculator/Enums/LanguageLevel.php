<?php

namespace App\Domain\Calculator\Enums;

enum LanguageLevel: string
{
    case B2 = 'B2';
    case C1 = 'C1';

    public function points(): int
    {
        return match ($this) {
            LanguageLevel::B2 => 28,
            LanguageLevel::C1 => 40,
        };
    }

    public function label(): string
    {
        return $this->value;
    }
}
