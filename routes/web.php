<?php

use Illuminate\Support\Facades\Route;
use Modules\Journals\Http\Controllers\EditorialBoardController;
use Modules\Journals\Http\Controllers\JournalsController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Journals Datatable, Bulk Actions & Export
    Route::get('journals/datatables', [JournalsController::class, 'dataForDatatables'])->name('journals.datatables');
    Route::post('journals/bulk-destroy', [JournalsController::class, 'bulkDestroy'])->name('journals.bulk-destroy');
    Route::get('journals/export', [JournalsController::class, 'export'])->name('journals.export');

    // Journals Resource CRUD
    Route::resource('journals', JournalsController::class)->names('journals');

    // Editorial Board Routes
    Route::post('editorial-boards', [EditorialBoardController::class, 'store'])->name('editorial-boards.store');
    Route::put('editorial-boards/{editorial_board}', [EditorialBoardController::class, 'update'])->name('editorial-boards.update');
    Route::delete('editorial-boards/{editorial_board}', [EditorialBoardController::class, 'destroy'])->name('editorial-boards.destroy');
});
