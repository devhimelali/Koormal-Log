<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:user', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return view('user.dashboard.index');
    })->name('user.dashboard');
});
