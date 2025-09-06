<?php

namespace App\Http\Controllers\Admin;

use App\Models\ShiftLog;
use App\Models\OpportuneJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\OpportuneJobImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\AddToLogFromOpportuneJobRequest;

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

    public function addToLogFromOpportuneJobs(AddToLogFromOpportuneJobRequest $request)
    {
        $ids = explode(',', $request->opportune_job_ids);

        try {
            DB::beginTransaction();

            $jobs = OpportuneJob::whereIn('id', $ids)->get();

            foreach ($jobs as $job) {
                ShiftLog::create([
                    'shift_name' => $request->shift,
                    'wo_number' => $job->wo_number,
                    'asset_no' => $job->asset_no,
                    'asset_description' => $job->asset_description,
                    'work_description' => $job->work_description,
                    'status' => $job->status,
                    'due_start' => $job->due_start,
                    'job_type' => $job->job_type,
                    'priority' => $job->priority,
                    'raised' => $job->raised,
                    'start_date' => $job->start_date,
                    'duration' => $job->duration,
                    'department' => $job->department,
                    'material_cost' => $job->material_cost,
                    'other_cost' => $job->other_cost,
                    'position' => ShiftLog::max('position') + 1,
                    'log_date' => $request->log_date,
                ]);
            }

            // Delete all jobs after logs are created
            OpportuneJob::whereIn('id', $ids)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Jobs added to shift log successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add jobs to shift log.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
