<?php

namespace App\Domain\Calculator\Http\Controllers;

use App\Domain\Calculator\Enums\Language;
use App\Domain\Calculator\Enums\LanguageLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class LanguageOptionsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'languages' => array_map(
                fn (Language $lang) => ['value' => $lang->value, 'label' => $lang->label()],
                Language::cases()
            ),
            'levels' => array_map(
                fn (LanguageLevel $level) => ['value' => $level->value, 'label' => $level->label()],
                LanguageLevel::cases()
            ),
        ]);
    }
}
