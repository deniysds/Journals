<?php

use Illuminate\Support\Facades\Route;
use Modules\Journals\Http\Controllers\JournalsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('journals', JournalsController::class)->names('journals');
});
