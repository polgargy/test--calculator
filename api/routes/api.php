<?php

use App\Domain\Calculator\Http\Controllers\CalculatorController;
use App\Domain\Calculator\Http\Controllers\InstitutionController;
use App\Domain\Calculator\Http\Controllers\LanguageOptionsController;
use App\Domain\Calculator\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/language-options', LanguageOptionsController::class)->only(['index']);
Route::apiResource('/institutions', InstitutionController::class)->only(['index']);
Route::apiResource('/required-subjects', SubjectController::class)->only(['index']);

Route::group(['prefix' => 'calculator', 'as' => 'calculator.'], function () {
    Route::post('/', [CalculatorController::class, 'calculate'])->name('calculate');
    Route::get('/students', [CalculatorController::class, 'index'])->name('students');
});
