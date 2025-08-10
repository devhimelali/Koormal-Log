<?php

use App\Http\Controllers\Admin\CrewController;
use App\Http\Controllers\Admin\HandoverCompletionController;
use App\Http\Controllers\Admin\HandoverCompletionQuestionController;
use App\Http\Controllers\Admin\LabourController;
use App\Http\Controllers\Admin\LabourShiftController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\OpportuneJobController;
use App\Http\Controllers\Admin\ScheduleComplianceController;
use App\Http\Controllers\admin\SupervisorCompletionComplianceController;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Admin\SupervisorNoteController;
use App\Http\Controllers\Admin\SupervisorsShiftLogController;
use App\Http\Controllers\Admin\WorkOrderMoveController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin|supervisor', 'verified'])->group(function () {
    Route::post('supervisors-shift-log',
        [SupervisorsShiftLogController::class, 'store'])->name('supervisors-shift-log.store');
    Route::get('supervisors-shift-log/{id}',
        [SupervisorsShiftLogController::class, 'show'])->name('supervisors-shift-log.show');

    Route::put('supervisors-shift-log/{id}',
        [SupervisorsShiftLogController::class, 'update'])->name('supervisors-shift-log.update');
    Route::put('shift-logs/update-details/{shift_log}',
        [SupervisorsShiftLogController::class, 'updateDetails'])->name('shift-logs.update-details');
    Route::get('shift-logs/mark-complete/{shift_log}',
        [SupervisorsShiftLogController::class, 'markComplete'])->name('shift-logs.markComplete');
    Route::get('shift-logs/reset-progress/{id}',
        [SupervisorsShiftLogController::class, 'resetProgress'])->name('shift-logs.resetProgress');

    Route::post('supervisors-shift-log/reorder',
        [SupervisorsShiftLogController::class, 'reorder'])->name('supervisors-shift-log.reorder');
    Route::delete('/supervisors-shift-log/{id}',
        [SupervisorsShiftLogController::class, 'destroy'])->name('supervisors-shift-log.destroy');
    Route::post('/supervisors-shift-log/import-csv',
        [SupervisorsShiftLogController::class, 'importShiftLog'])->name('supervisors-shift-log.csv.import');
    Route::get('/export-shift-logs',
        [SupervisorsShiftLogController::class, 'export'])->name('supervisors-shift-log.export');
    Route::post('/labour-supervisor/copy',
        [SupervisorsShiftLogController::class, 'copyToNextDays'])->name('labour-supervisor.copy');

    Route::get('update-critical-work/{id}',
        [SupervisorsShiftLogController::class, 'updateCriticalWork'])->name('update-critical-work');
    // Labourer routes
    Route::post('supervisor-shift/update',
        [SupervisorController::class, 'updateSupervisor'])->name('supervisor-shift.update');

    Route::resource('notes', NoteController::class);


    Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('supervisor-notes/create', [SupervisorNoteController::class, 'create'])->name('supervisor-notes.create');
    Route::Post('supervisor-notes', [SupervisorNoteController::class, 'store'])->name('supervisor-notes.store');
    Route::get('supervisor-notes/export-pdf/{log_date}/{note_type}',
        [SupervisorNoteController::class, 'exportPdf'])->name('supervisor-notes.pdf');;
    Route::post('/supervisor-notes/delete-image',
        [SupervisorNoteController::class, 'deleteImage'])->name('supervisor-notes.delete-image');
    Route::post('bulk-delete-supervisor-shift',
        [SupervisorsShiftLogController::class, 'bulkDelete'])->name('bulk-delete-supervisor-shift');
    Route::resource('opportune-jobs', OpportuneJobController::class);
    Route::post('bulk-import-opportune-jobs',
        [OpportuneJobController::class, 'bulkImportFromExcel'])->name('bulk-import-opportune-jobs');
    Route::post('store-shift-log-from-opportune-jobs', [
        SupervisorsShiftLogController::class, 'storeShiftLogFromOpportuneJobs'
    ])->name('store-shift-log-from-opportune-jobs');
    Route::resource('handover-completion-questions', HandoverCompletionQuestionController::class);
    Route::get('handover-completions',
        [HandoverCompletionController::class, 'index'])->name('handover-completions.index');
    Route::get('handover-completions/create',
        [HandoverCompletionController::class, 'create'])->name('handover-completions.create');
    Route::post('handover-completions',
        [HandoverCompletionController::class, 'store'])->name('handover-completions.store');
    Route::get('handover-completions/{id}',
        [HandoverCompletionController::class, 'show'])->name('handover-completions.show');
    Route::resource('crews', CrewController::class)->except('show');
    Route::resource('labours', LabourController::class)->except('show');
    Route::get('load-crew', [CrewController::class, 'loadCrew'])->name('load-crew.index');
    Route::get('get-labour-by-crew/{id}', [CrewController::class, 'getLabourByCrew'])->name('get-labour-by-crew');
    Route::post('labour-shift', [LabourShiftController::class, 'store'])->name('load-crew.store');
    Route::post('labour-shift/update', [LabourShiftController::class, 'updateLabour'])->name('labour-shift.update');
    Route::resource('work-order-moves', WorkOrderMoveController::class);
    Route::delete('bulk-delete-opportune-job',
        [OpportuneJobController::class, 'bulkDestroy'])->name('bulk-delete-opportune-job');
    Route::post('add-to-log-from-opportune-jobs',
        [OpportuneJobController::class, 'addToLogFromOpportuneJobs'])->name('add-to-log-from-opportune-jobs');
    Route::get('load-labour', [CrewController::class, 'getLabours'])->name('load-labour.index');
    Route::post('load-labour', [CrewController::class, 'storeLabourShift'])->name('load-labour.store');
    Route::get('reset-progress/{id}',
        [SupervisorsShiftLogController::class, 'resetJobProgress'])->name('reset-progress');
    Route::get('/shift-log/progress-graph',
        [ScheduleComplianceController::class, 'dailyProgressGraph'])->name('shift-log.progress-graph');
    Route::get('/schedule-compliance',
        [ScheduleComplianceController::class, 'index'])->name('schedule-compliance.index');
    Route::get('/supervisor-completion-compliance',
        [SupervisorCompletionComplianceController::class, 'index'])->name('supervisor-completion-compliance.index');
    Route::post('/supervisor-completion-compliance/pdf',
        [SupervisorCompletionComplianceController::class, 'exportPdf'])->name('supervisor-completion-compliance.pdf');
});
