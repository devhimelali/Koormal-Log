<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\Admin\SupervisorsShiftLog;

Route::get('/redirect', [RedirectController::class, 'redirect'])->name('redirect')->middleware('auth');
Route::get('{role}/profile', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::get('/test', function () {
    return view('test');
})->middleware(['auth', 'verified'])->name('test');

Route::get('admin/supervisors-shift-log', [SupervisorsShiftLog::class, 'index'])->name('supervisors-shift-log.index');
Route::get('admin/supervisors-shift-log/{id}', [SupervisorsShiftLog::class, 'show'])->name('supervisors-shift-log.show');
