<?php

use App\Http\Controllers\admin\ShiftLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard');
    Route::resource('supervisors-shift-log', ShiftLogController::class);
    Route::post('supervisors-shift-log/bulk-import', [ShiftLogController::class, 'bulkImport'])->name('supervisors-shift-log.bulk-import');
});
