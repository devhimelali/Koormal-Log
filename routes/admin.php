<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SupervisorsShiftLog;

Route::middleware(['auth', 'role:admin', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard');
});


Route::get('supervisors-shift-log', [SupervisorsShiftLog::class, 'index'])->name('supervisors-shift-log.index');
Route::post('supervisors-shift-log', [SupervisorsShiftLog::class, 'store'])->name('supervisors-shift-log.store');
Route::get('supervisors-shift-log/{id}', [SupervisorsShiftLog::class, 'show'])->name('supervisors-shift-log.show');
Route::put('supervisors-shift-log/{id}', [SupervisorsShiftLog::class, 'update'])->name('supervisors-shift-log.update');
Route::delete('/supervisors-shift-log/{id}', [SupervisorsShiftLog::class, 'destroy'])->name('supervisors-shift-log.destroy');
Route::post('/supervisors-shift-log/import-csv', [SupervisorsShiftLog::class, 'importShiftLog'])->name('supervisors-shift-log.csv.import');
