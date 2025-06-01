<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;


Route::get('/redirect', [RedirectController::class, 'redirect'])->name('redirect')->middleware('auth');
Route::get('{role}/profile', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::get('/test', function () {
    return view('test');
})->middleware(['auth', 'verified'])->name('test');
