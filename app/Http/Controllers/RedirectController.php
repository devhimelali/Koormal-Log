<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function redirect(Request $request)
    {
        if (Auth::check()) {
            $role = Auth::user()->roles->pluck('name')->first();
            if ($role == 'user') {
                return redirect()->route('user.dashboard');
            }

            return redirect()->route("supervisors-shift-log.index", [
                'role' => $role,
                'date' => date('d-m-Y')
            ]);
        }
    }
}
