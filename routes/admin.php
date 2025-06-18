<?php

use App\Http\Controllers\Admin\HandoverCompletionController;
use App\Http\Controllers\Admin\LabourController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\OpportuneJobController;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Admin\SupervisorNoteController;
use App\Http\Controllers\Admin\SupervisorsShiftLogController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return redirect()->route('supervisors-shift-log.index', ['date' => date('d-m-Y')]);
        return view('admin.dashboard.index');
    })->name('admin.dashboard');

    Route::get('supervisors-shift-log', [SupervisorsShiftLogController::class, 'index'])->name('supervisors-shift-log.index');
    Route::post('supervisors-shift-log', [SupervisorsShiftLogController::class, 'store'])->name('supervisors-shift-log.store');
    Route::get('supervisors-shift-log/{id}', [SupervisorsShiftLogController::class, 'show'])->name('supervisors-shift-log.show');

    Route::put('supervisors-shift-log/{id}', [SupervisorsShiftLogController::class, 'update'])->name('supervisors-shift-log.update');
    Route::put('shift-logs/update-details/{shift_log}', [SupervisorsShiftLogController::class, 'updateDetails'])->name('shift-logs.update-details');
    Route::get('shift-logs/mark-complete/{shift_log}', [SupervisorsShiftLogController::class, 'markComplete'])->name('shift-logs.markComplete');

    Route::post('supervisors-shift-log/reorder', [SupervisorsShiftLogController::class, 'reorder'])->name('supervisors-shift-log.reorder');
    Route::delete('/supervisors-shift-log/{id}', [SupervisorsShiftLogController::class, 'destroy'])->name('supervisors-shift-log.destroy');
    Route::post('/supervisors-shift-log/import-csv', [SupervisorsShiftLogController::class, 'importShiftLog'])->name('supervisors-shift-log.csv.import');
    Route::get('/export-shift-logs', [SupervisorsShiftLogController::class, 'export'])->name('supervisors-shift-log.export');

    // Labourer routes
    Route::post('labour-shift/update', [LabourController::class, 'updateLabour'])->name('labour-shift.update');
    Route::post('supervisor-shift/update', [SupervisorController::class, 'updateSupervisor'])->name('supervisor-shift.update');

    Route::resource('notes', NoteController::class);


    Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('supervisor-notes/create', [SupervisorNoteController::class, 'create'])->name('supervisor-notes.create');
    Route::Post('supervisor-notes', [SupervisorNoteController::class, 'store'])->name('supervisor-notes.store');
    Route::post('/supervisor-notes/delete-image', [SupervisorNoteController::class, 'deleteImage'])->name('supervisor-notes.delete-image');
    Route::post('bulk-delete-supervisor-shift', [SupervisorsShiftLogController::class, 'bulkDelete'])->name('bulk-delete-supervisor-shift');
    Route::resource('opportune-jobs', OpportuneJobController::class);
    Route::post('bulk-import-opportune-jobs', [OpportuneJobController::class, 'bulkImportFromExcel'])->name('bulk-import-opportune-jobs');
    Route::post('store-shift-log-from-opportune-jobs', [SupervisorsShiftLogController::class, 'storeShiftLogFromOpportuneJobs'])->name('store-shift-log-from-opportune-jobs');
    Route::get('handover-completions', [HandoverCompletionController::class, 'index'])->name('handover-completions.index');
    Route::get('handover-completions/create', [HandoverCompletionController::class, 'create'])->name('handover-completions.create');
    Route::post('handover-completions', [HandoverCompletionController::class, 'store'])->name('handover-completions.store');
    Route::get('handover-completions/{id}', [HandoverCompletionController::class, 'show'])->name('handover-completions.show');
});
