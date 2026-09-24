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

    // Editorial Board Routes & Datatables
    Route::get('editorial-boards/datatables', [EditorialBoardController::class, 'dataForDatatables'])->name('editorial-boards.datatables');
    Route::post('editorial-boards/bulk-destroy', [EditorialBoardController::class, 'bulkDestroy'])->name('editorial-boards.bulk-destroy');
    Route::get('editorial-boards/export', [EditorialBoardController::class, 'export'])->name('editorial-boards.export');
    Route::get('editorial-boards', [EditorialBoardController::class, 'index'])->name('editorial-boards.index');
    Route::post('editorial-boards', [EditorialBoardController::class, 'store'])->name('editorial-boards.store');
    Route::put('editorial-boards/{editorial_board}', [EditorialBoardController::class, 'update'])->name('editorial-boards.update');
    Route::delete('editorial-boards/{editorial_board}', [EditorialBoardController::class, 'destroy'])->name('editorial-boards.destroy');

    // Manajemen Pengumuman & Call for Papers Jurnal
    Route::prefix('admin/announcements')->name('announcements.')->group(function () {
        Route::get('/', [\Modules\Journals\Http\Controllers\AdminAnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [\Modules\Journals\Http\Controllers\AdminAnnouncementController::class, 'create'])->name('create');
        Route::post('/', [\Modules\Journals\Http\Controllers\AdminAnnouncementController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\Modules\Journals\Http\Controllers\AdminAnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\Modules\Journals\Http\Controllers\AdminAnnouncementController::class, 'update'])->name('update');
        Route::delete('/{id}', [\Modules\Journals\Http\Controllers\AdminAnnouncementController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle', [\Modules\Journals\Http\Controllers\AdminAnnouncementController::class, 'toggleStatus'])->name('toggle');
    });
});
