<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\OpportuneJobImport;
use App\Models\OpportuneJob;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class OpportuneJobController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = OpportuneJob::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('work_description', function ($row) {
                    return nl2br($row->work_description);
                })
                ->editColumn('asset_description', function ($row) {
                    return nl2br($row->asset_description);
                })
                ->addColumn('actions', function ($row) {
                    $checkbox = '<input type="checkbox" class="row-checkbox p-2" style="width: 18px; height: 18px;" value="' . $row->id . '">';
                    $deleteBtn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="delete btn btn-danger btn-sm me-2">Delete</a>';

                    return '<div class="d-flex align-items-center justify-content-center">' . $deleteBtn . $checkbox . '</div>';
                })
                ->rawColumns(['work_description', 'asset_description', 'actions'])
                ->make(true);
        }
        return view('admin.opportune-jobs.index');
    }

    public function destroy(OpportuneJob $opportuneJob)
    {
        $opportuneJob->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Opportune job deleted successfully.'
        ]);
    }

    public function bulkDestroy()
    {
        $deleted = OpportuneJob::truncate();

        return response()->json([
            'status' => 'success',
            'message' => 'All opportune jobs have been deleted successfully.'
        ]);
    }

    public function bulkImportFromExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('file');
            $fileName = 'import-opportune-jobs-' . time() . '-' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/imports/opportune-jobs', $fileName, 'public');
            Excel::import(new OpportuneJobImport(), storage_path('app/public/' . $filePath));

            return response()->json([
                'status' => 'success',
                'message' => 'Opportune jobs imported successfully.'
            ], 201);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            if (
                str_contains($errorMessage, 'Undefined index') || str_contains(
                    $errorMessage,
                    'Trying to access array offset'
                )
            ) {
                $friendlyMessage = 'The uploaded file appears to be missing some expected columns.';
            } elseif (str_contains($errorMessage, 'File does not exist')) {
                $friendlyMessage = 'The uploaded file could not be found. Please try again.';
            } elseif (
                str_contains($errorMessage, 'PHPExcel_Reader_Exception') || str_contains(
                    $errorMessage,
                    'Reader'
                )
            ) {
                $friendlyMessage = 'There was a problem reading the Excel file. Please ensure it is a valid .xlsx or .xls file.';
            } elseif (
                str_contains($errorMessage, 'SQLSTATE') || str_contains(
                    $errorMessage,
                    'database'
                ) || str_contains($errorMessage, 'Integrity constraint')
            ) {
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
