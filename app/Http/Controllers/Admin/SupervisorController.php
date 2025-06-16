<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupervisorRequest;
use App\Models\Supervisor;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function updateSupervisor(SupervisorRequest $request)
    {
        Supervisor::updateOrCreate(
            [
                'date' => $request->date,
                'shift' => $request->shift
            ],
            [
                'name' => $request->supervisor
            ]
        );

        return response()->json(['message' => 'Supervisor list updated successfully.']);

    }
}
