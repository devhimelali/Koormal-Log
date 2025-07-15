<?php

namespace App\DataTables;

use App\Models\Note;
use Carbon\Carbon;
use App\Models\ShiftLog;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Column;


class ShiftLogsDataTable extends DataTable
{
    public function dataTable($query)
    {
        $role = Auth::user()->roles()->pluck('name')->first();
        $index = 0;
        return datatables()
            ->eloquent($query)
            ->setRowAttr([
                'data-id' => function ($job) {
                    return $job->id;
                },
            ])
            ->setRowId(fn($row) => 'row_' . $row->id)
            ->setRowClass(function ($job) {
                if ($job->mark_as_complete == 1 && $job->shift_name === 'day') {
                    return 'day-row-complete';
                } else if ($job->mark_as_complete == 1 && $job->shift_name === 'night') {
                    return 'night-row-complete';
                }

                if ($job->shift_name === 'night') {
                    return 'row-night';
                }

                return '';
            })
            ->addIndexColumn()
            ->addColumn('line', function ($job) use (&$index) {
                $index++; // Increment each row
                return '
                    <span class="line-no-text">' . $index . '</span>
                    <span class="drag-handle" style="cursor: move; margin-left: 6px;">
                        <i class="bi bi-arrows-move"></i>
                    </span>';
            })
            ->addColumn('shift', function ($job) use ($role) {
                $disabled = (($role != 'admin' && $job->isLocked) || $job->mark_as_complete == 1) ? 'disabled' : '';
                $selectedDay = $job->shift_name === 'day' ? 'selected' : '';
                $selectedNight = $job->shift_name === 'night' ? 'selected' : '';
                $style = match (true) {
                    $job->mark_as_complete == 1 && $job->shift_name === 'day' => 'background-color: #ffef3bc2;',
                    $job->mark_as_complete == 1 && $job->shift_name === 'night' => 'background-color: #4a91e29d;',
                    $job->shift_name === 'night' => 'background-color: #939393a8;',
                    default => '',
                };
                return '<select class="form-select form-select-sm shift_name" ' . $disabled . ' data-field="shift_name" style="text-transform: capitalize; width: 100%; font-size: 10px;' . $style . '">
                        <option value="">Select Shift</option>
                        <option value="day" ' . $selectedDay . '>Day</option>
                        <option value="night" ' . $selectedNight . '>Night</option>
                    </select>';
            })
            ->addColumn('wo_number', function ($job) {
                $editable = $job->is_excel_upload === 0 ? 'contenteditable=true' : '';
                return "<div {$editable} data-field='wo_number' class='py-3 m-0'>{$job->wo_number}</div>";
            })
            ->addColumn('asset_no', function ($job) {
                $editable = $job->is_excel_upload === 0 ? 'contenteditable=true' : '';
                return "<div {$editable} data-field='asset_no' class='py-3 m-0'>{$job->asset_no}</div>";
            })
            ->addColumn('work_description', function ($job) {
                $editable = $job->is_excel_upload === 0 ? 'contenteditable=true' : '';
                return "<div {$editable} data-field='work_description' class='py-3 m-0'>{$job->work_description}</div>";
            })
            ->addColumn('labour', function ($job) use ($role) {
                $editable = ($role != 'admin' && $job->isLocked) ? '' : "contenteditable=true";
                return "<div {$editable} data-field='labour' class='py-3 m-0'>{$job->labour}</div>";
            })
            ->addColumn('note', function ($job) use ($role) {
                $notes = Note::orderBy('sort_by', 'asc')->get();
                $options = '<option value="">Select Note</option>';

                foreach ($notes as $note) {
                    $selected = $job->note_id == $note->id ? 'selected' : '';
                    $shortText = strlen($note->note) > 25 ? substr($note->note, 0, 25) . '...' : $note->note;
                    $options .= "<option value=\"{$note->id}\" {$selected}>{$shortText}</option>";
                }
                $disabled = (($role != 'admin' && $job->isLocked) || $job->mark_as_complete == 1) ? 'disabled' : '';
                return '
                    <select
                        class="form-select form-select-sm shift_name" ' . $disabled . '
                        data-field="note_id"
                        data-id="' . $job->id . '"
                        style="
                            font-size: 11px;
                            width: 100%;
                            text-transform: capitalize;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                            background-position: right 0.5rem center; /* move arrow icon */
                            background-repeat: no-repeat;
                            background-size: 12px;
                        ">
                        ' . $options . '
                    </select>';
            })
            ->addColumn('requisition', function ($job) use ($role) {
                $selected = $job->requisition === 'yes' ? 'selected' : '';
                $disabled = (($role != 'admin' && $job->isLocked) || $job->mark_as_complete == 1) ? 'disabled' : '';
                return '<select class="form-control w-100 shift_name" data-field="requisition" style="text-transform: capitalize; font-size: 10px;" ' . $disabled . '>
                            <option value="no" ' . $selected . '>No</option>
                            <option value="yes" ' . $selected . '>Yes</option>
                        </select>';
            })
            ->editColumn('scheduled', function ($job) {
                $role = Auth::user()->roles()->pluck('name')->first();
                $disabled = $role !== 'admin' ? 'disabled' : '';
                return '
        <select class="form-select form-select-sm shift_name scheduled" ' . $disabled . ' data-field="scheduled" style="text-transform: capitalize; width: 100%; font-size: 10px;">
            <option value="">Select Scheduled</option>
            <option value="yes" ' . ($job->scheduled == 'yes' ? 'selected' : '') . '>Yes</option>
            <option value="no" ' . ($job->scheduled == 'no' ? 'selected' : '') . '>No</option>
        </select>
    ';
            })

            ->addColumn('progress', function ($job) use ($role) {
                $disabled = (($role != 'admin' && $job->isLocked) || $job->mark_as_complete == 1) ? 'disabled' : '';
                $resetClass = 'reset-progress' . ($disabled == 'disabled' ? '' : ' d-none');

                return '<div style="position: relative; display: inline-block;" >
                <input data-field="progress" type="number"
                       class="form-control text-center complete_progress"
                       min="0" max="100" value="' . $job->progress . '"
                       style="width: 68px; padding-right: 24px;"
                       ' . $disabled . '
                       oninput="this.value = Math.max(0, Math.min(100, this.value))">
                <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;">%</span>
                <span data-id="' . $job->id . '" class="' . $resetClass . '" style="position: absolute; right: 51px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </span>
            </div>';
            })
            ->addColumn('action', function ($job) use ($role) {
                $disabled = (($role != 'admin' && $job->isLocked) || $job->mark_as_complete == 1) ? 'disabled' : '';
                $url = route('supervisors-shift-log.show', $job->id);

                $buttons = '
        <div class="btn-group">
            <a href="' . $url . '" class="btn btn-sm btn-info">
                <i class="bi bi-info-circle"></i> More
            </a>

            <button class="btn btn-warning btn-sm move-work-order-number-btn"
                data-id="' . $job->id . '"
                data-shift="' . $job->shift_name . '"
                data-date="' . $job->log_date . '"
                data-wo-number="' . $job->wo_number . '"
                data-asset-no="' . $job->asset_no . '"
                ' . $disabled . '>
                <i class="bi bi-arrows-move me-2"></i> Move
            </button>';

                if ($role == 'admin') {
                    $buttons .= '
            <button class="btn btn-sm btn-danger deleteRowBtn"
                data-id="' . $job->id . '" ' . $disabled . '>
                <i class="bi bi-trash"></i> Delete
            </button>';
                }

                $buttons .= '</div>';

                return $buttons;
            })

            ->rawColumns(['line', 'requisition', 'shift', 'wo_number', 'note', 'asset_no', 'work_description', 'labour', 'scheduled', 'progress', 'action']);
    }

    public function query()
    {
        $query = ShiftLog::query();

        // Custom ordering: day shifts (first), then night shifts
        $query->orderByRaw("
        CASE 
            WHEN shift_name = 'day' THEN 1
            WHEN shift_name = 'night' THEN 2
            ELSE 3
        END
    ")->orderBy('wo_number', 'asc');

        // Extra order by 'position' when not ordering a specific column
        $orderColumnIndex = $this->request->input('order.0.column');
        $orderColumnName = $this->getColumns()[$orderColumnIndex]['data'] ?? null;

        if (!$orderColumnName || $orderColumnName === 'line') {
            $query->orderBy('position');
        }

        // Filter by shift if requested
        if (request()->has('shift') && in_array(request('shift'), ['day', 'night'])) {
            $query->where('shift_name', request('shift'));
        }

        // Filter by date — default to today
        $rawDate = request('date');

        if (filled($rawDate)) {
            try {
                $queryDate = Carbon::createFromFormat('d-m-Y', $rawDate)->format('d-m-Y');
            } catch (\Exception $e) {
                $queryDate = now()->format('d-m-Y');
            }
        } else {
            $queryDate = now()->format('d-m-Y');
        }

        $query->where('log_date', $queryDate);

        return $query;
    }



    public function html()
    {
        return $this->builder()
            ->setTableId('jobTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'scrollX' => true,
                'paging' => false,
                'searching' => false,
                'ordering' => true,
                'responsive' => true,
                'order' => [[1, 'asc']],
            ]);
    }

    protected function getColumns()
    {
        $columns = [
            Column::make('line')
                ->title('#')
                ->orderable(false)
                ->searchable(false)
                ->width('40px')
                ->addClass('align-content-center'),

            Column::make('shift')
                ->name('shift_name')
                ->title('Shift')
                ->orderable(true)
                ->searchable(false)
                ->width('60px')
                ->addClass('align-content-center col-shift'),

            Column::make('wo_number')
                ->title('WO Number')
                ->orderable(false)
                ->searchable(false)
                ->addClass('align-content-center'),

            Column::make('asset_no')
                ->title('Asset No')
                ->orderable(false)
                ->searchable(false)
                ->addClass('align-content-center'),

            Column::make('work_description')
                ->title('Work Description')
                ->orderable(false)
                ->searchable(false)
                ->width('300px')
                ->addClass('align-content-center'),

            Column::make('labour')
                ->title('Labour Assigned')
                ->orderable(false)
                ->searchable(false)
                ->width('200px')
                ->addClass('text-wrap align-content-center'),

            Column::make('note')
                ->title('Note')
                ->orderable(false)
                ->searchable(false)
                ->width('200px')
                ->addClass('col-note'),

            Column::make('requisition')
                ->title('Req')
                ->orderable(false)
                ->searchable(false)
                ->addClass('col-req'),


        ];
        // Add action column conditionally
        $role = Auth::user()->roles()->pluck('name')->first();
        $dayShiftLogs = ShiftLog::where('log_date', request('date'))->where('shift_name', 'day')->get()->toArray();
        $dayShiftScheduledYesPercentage = $this->calculateScheduledYesPercentage($dayShiftLogs);
        $nightShiftLogs = ShiftLog::where('log_date', request('date'))->where('shift_name', 'night')->get()->toArray();
        $nightShiftScheduledYesPercentage = $this->calculateScheduledYesPercentage($nightShiftLogs);

        $columns[] = Column::make('scheduled')
            ->title('<div class="d-flex flex-column align-items-center justify-content-center" style="min-width: 100px; width: 100px">
                <span>Scheduled</span>
                <span class="fw-light" style="font-size: 12px; color: red;">Day: ' . $dayShiftScheduledYesPercentage . '%</span>
                <span class="fw-light" style="font-size: 12px; color: red;">Night: ' . $nightShiftScheduledYesPercentage . '%</span>
                </div>')
            ->orderable(false)
            ->searchable(false)
            ->addClass('align-content-center');


        $columns[] = Column::make('progress')
            ->title('% Complete')
            ->orderable(false)
            ->searchable(false)
            ->addClass('align-content-center');


        if ($role === 'admin') {
            $columns[] = Column::computed('action')
                ->title('<div class="d-flex align-items-center justify-content-center gap-3">
                        <div>Action</div>
                        <div><button id="delete-selected" class="btn btn-sm btn-danger">Delete All</button></div>
                    </div>')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width('280px')
                ->addClass('text-center align-content-center');
        } else {
            $columns[] = Column::computed('action')
                ->title('<div class="d-flex align-items-center justify-content-center gap-3">
                        <div>Action</div>
                    </div>')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width('280px')
                ->addClass('text-center align-content-center');
        }

        return $columns;
    }

    private function calculateScheduledYesPercentage(?array $jobs): float
    {
        // dd($jobs);
        if (empty($jobs)) {
            return 0.0;
        }

        // Filter jobs where 'scheduled' is 'yes'
        $filteredJobs = array_filter($jobs, function ($job) {
            return isset($job['scheduled'], $job['progress']) && strtolower($job['scheduled']) === 'yes';
        });

        $total = count($filteredJobs);

        if ($total === 0) {
            return 0.0;
        }

        // Sum the 'progress' values
        $totalProgress = array_reduce($filteredJobs, function ($carry, $job) {
            return $carry + (float) $job['progress'];
        }, 0.0);

        // Calculate percentage
        $percentage = ($totalProgress / $total);

        return round($percentage, 2);
    }

}
