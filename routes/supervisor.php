<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SupervisorsShiftLogController;

Route::middleware(['auth', 'role:supervisor', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return view('supervisor.dashboard.index');
    })->name('supervisor.dashboard');

    // Route::get('supervisors-shift-log', [SupervisorsShiftLogController::class, 'index'])->name('supervisors-shift-log.index');
});
