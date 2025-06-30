<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\Admin\SupervisorsShiftLogController;


Route::get('/redirect', [RedirectController::class, 'redirect'])->name('redirect')->middleware('auth');
Route::get('{role}/profile', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->roles->pluck('name')->first();

        if ($role == 'user') {
            return redirect()->route('user.dashboard');
        }

        return redirect()->route('supervisors-shift-log.index', ['role' => $role, 'date' => date('d-m-Y')]);
    }
    return view('auth.login');
})->name('login');


Route::get('user/dashboard', function () {
    return view("user.dashboard.index");
})->middleware(['auth', 'role:user', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin|supervisor', 'verified'])->group(function () {
    Route::get('{role}/supervisors-shift-log', [SupervisorsShiftLogController::class, 'index'])->name('supervisors-shift-log.index');
});
