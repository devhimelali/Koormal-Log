<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupervisorsShiftLog extends Controller
{
    public function index()
    {
        $jobs = [
            [
                'id' => '1',
                'shift' => 'Day',
                'wo_number' => 'WO1001',
                'asset_no' => 'A-123',
                'description' => 'Inspect Pump',
                'labour' => 'John',
            ],
            [
                'id' => '2',
                'shift' => 'Night',
                'wo_number' => 'WO1002',
                'asset_no' => 'A-456',
                'description' => 'Clean Filter',
                'labour' => 'Jane',
            ],
            [
                'id' => '3',
                'shift' => 'Day',
                'wo_number' => 'WO1003',
                'asset_no' => 'A-789',
                'description' => 'Replace Valve',
                'labour' => 'Mark',
            ],
            [
                'id' => '4',
                'shift' => 'Night',
                'wo_number' => 'WO1004',
                'asset_no' => 'B-111',
                'description' => 'Check Motor',
                'labour' => 'Albert',
            ],
            [
                'id' => '5',
                'shift' => 'Day',
                'wo_number' => 'WO1005',
                'asset_no' => 'C-222',
                'description' => 'Oil Change',
                'labour' => 'Steven',
            ],
            [
                'id' => '6',
                'shift' => 'Night',
                'wo_number' => 'WO1006',
                'asset_no' => 'D-333',
                'description' => 'Inspect Conveyor',
                'labour' => 'Ralph',
            ],
            [
                'id' => '7',
                'shift' => 'Day',
                'wo_number' => 'WO1007',
                'asset_no' => 'E-444',
                'description' => 'Adjust Belt',
                'labour' => 'Bill',
            ],
            [
                'id' => '8',
                'shift' => 'Night',
                'wo_number' => 'WO1008',
                'asset_no' => 'F-555',
                'description' => 'Test Generator',
                'labour' => 'John',
            ],
            [
                'id' => '9',
                'shift' => 'Day',
                'wo_number' => 'WO1009',
                'asset_no' => 'G-666',
                'description' => 'Clean Vent',
                'labour' => 'Alex',
            ],
            [
                'id' => '10',
                'shift' => 'Night',
                'wo_number' => 'WO1010',
                'asset_no' => 'H-777',
                'description' => 'Fix Wiring',
                'labour' => 'Albert',
            ],
        ];
        return view('admin.supervisors.supervisors-shift-log', compact('jobs'));
    }

    public function show($id)
    {
        return view('admin.supervisors.supervisors-shift-log-show');
    }
}
