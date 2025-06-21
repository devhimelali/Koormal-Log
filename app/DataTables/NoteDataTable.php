<?php

namespace App\DataTables;

use App\Models\Note;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class NoteDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($job) {
                $url = route('supervisors-shift-log.show', $job->id);
                return '
                    <div class="btn-group">
                        <a href="javascript:void(0);" class="btn btn-sm btn-secondary editBtn" data-id="' . $job->id . '">
                            <i class="bi bi-pencil me-2"></i> Edit
                        </a>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $job->id . '">
                            <i class="bi bi-trash me-2"></i> Delete
                        </button>
                    </div>';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Note>
     */
    public function query(Note $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('notes-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['title' => 'Action', 'width' => '138px', 'printable' => false])
            ->parameters([
                'order' => [[2, 'asc']],
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns()
    {
        return [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false, 'style' => "width: 23px !important;"],
            ['data' => 'note', 'name' => 'note', 'title' => 'Name'],
            ['data' => 'sort_by', 'name' => 'sort_by', 'title' => 'Sorting Order', 'className' => 'text-center sort-by-column',],
        ];
    }
}
