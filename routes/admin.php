<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\Admin\SupervisorsShiftLog;

Route::middleware(['auth', 'role:admin', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard');



    Route::get('supervisors-shift-log', [SupervisorsShiftLog::class, 'index'])->name('supervisors-shift-log.index');
    Route::post('supervisors-shift-log', [SupervisorsShiftLog::class, 'store'])->name('supervisors-shift-log.store');
    Route::get('supervisors-shift-log/{id}', [SupervisorsShiftLog::class, 'show'])->name('supervisors-shift-log.show');

    Route::put('supervisors-shift-log/{id}', [SupervisorsShiftLog::class, 'update'])->name('supervisors-shift-log.update');
    Route::put('shift-logs/update-details/{shift_log}', [SupervisorsShiftLog::class, 'updateDetails'])->name('shift-logs.update-details');
    Route::get('shift-logs/mark-complete/{shift_log}', [SupervisorsShiftLog::class, 'markComplete'])->name('shift-logs.markComplete');



    Route::post('supervisors-shift-log/reorder', [SupervisorsShiftLog::class, 'reorder'])->name('supervisors-shift-log.reorder');
    Route::delete('/supervisors-shift-log/{id}', [SupervisorsShiftLog::class, 'destroy'])->name('supervisors-shift-log.destroy');
    Route::post('/supervisors-shift-log/import-csv', [SupervisorsShiftLog::class, 'importShiftLog'])->name('supervisors-shift-log.csv.import');
    Route::get('/export-shift-logs', [SupervisorsShiftLog::class, 'export'])->name('supervisors-shift-log.export');


    Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');
});
