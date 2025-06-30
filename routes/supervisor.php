<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:supervisor', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return view('supervisor.dashboard.index');
    })->name('supervisor.dashboard');
});
