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

        $shift = $request->shift == 'day' ? 'Day Shift' : 'Night Shift';

        return response()->json([
            'status' => 'success',
            'message' => 'Supervisor ' . $shift . ' list updated successfully.'
        ]);

    }
}
