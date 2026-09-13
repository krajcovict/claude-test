<?php

use App\Http\Controllers\Admin\JdfImportController;
use App\Http\Controllers\LinkaTimetableController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/linky', [LinkaTimetableController::class, 'index'])->name('linky.index');
Route::get('/linky/{cisloLinky}/{rozliseniLinky}', [LinkaTimetableController::class, 'show'])->name('linky.show');

// Merge into your existing routes (e.g. routes/web.php), don't overwrite it.
// Wrap this group in your real admin auth middleware, e.g.:
//   Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () { ... });
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/jdf-import', [JdfImportController::class, 'index'])->name('jdf-import.index');
    Route::post('/jdf-import', [JdfImportController::class, 'store'])->name('jdf-import.store');
});
