<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Imports\ShiftLogImport;
use App\Models\ShiftLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ShiftLogController extends Controller
{
    public function index()
    {
        $jobs = ShiftLog::get();
        return view('admin.supervisors-shift-log.index', compact('jobs'));
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        try {
            // Save the uploaded file
            $file = $request->file('file');
            $fileName = 'import-'.time().'-'.$file->getClientOriginalName();
            $filePath = 'uploads/imports/shift-logs';
            $file->move(public_path($filePath), $fileName);

            // Import the shift log data into the database
            Excel::import(new ShiftLogImport(), public_path($filePath.'/'.$fileName));

            return response()->json([
                'status' => 'success',
                'message' => 'Shift logs imported successfully.'
            ], 201);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Undefined index') || str_contains($errorMessage,
                    'Trying to access array offset')) {
                $friendlyMessage = 'The uploaded file appears to be missing some expected columns.';
            } elseif (str_contains($errorMessage, 'File does not exist')) {
                $friendlyMessage = 'The uploaded file could not be found. Please try again.';
            } elseif (str_contains($errorMessage, 'PHPExcel_Reader_Exception') || str_contains($errorMessage,
                    'Reader')) {
                $friendlyMessage = 'There was a problem reading the Excel file. Please ensure it is a valid .xlsx or .xls file.';
            } elseif (str_contains($errorMessage, 'SQLSTATE') || str_contains($errorMessage,
                    'database') || str_contains($errorMessage, 'Integrity constraint')) {
                $friendlyMessage = 'A database error occurred. Please ensure your file contains valid and unique data.';
            } else {
                $friendlyMessage = 'An unexpected error occurred during import. Please check your file and try again.';
            }

            return response()->json([
                'status' => 'error',
                'message' => $friendlyMessage
            ], 500);
        }
    }
}
