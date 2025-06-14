<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;


Route::get('/redirect', [RedirectController::class, 'redirect'])->name('redirect')->middleware('auth');
Route::get('{role}/profile', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('supervisors-shift-log.index', ['date' => date('d-m-Y')]);
    }
    return view('auth.login');
})->name('login');
