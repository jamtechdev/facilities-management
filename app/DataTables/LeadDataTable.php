<?php

namespace App\DataTables;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LeadDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('stage_badge', function (Lead $lead) {
                $stageColors = [
                    'new_lead' => 'primary',
                    'in_progress' => 'info',
                    'qualified' => 'success',
                    'not_qualified' => 'warning',
                    'junk' => 'danger'
                ];
                $stageLabels = [
                    'new_lead' => 'New Lead',
                    'in_progress' => 'In Progress',
                    'qualified' => 'Qualified',
                    'not_qualified' => 'Not Qualified',
                    'junk' => 'Junk'
                ];
                $color = $stageColors[$lead->stage] ?? 'secondary';
                $label = $stageLabels[$lead->stage] ?? $lead->stage;
                return new HtmlString('<span class="badge bg-' . $color . '">' . $label . '</span>');
            })
            ->addColumn('assigned_staff_name', function (Lead $lead) {
                return $lead->assignedStaff ? $lead->assignedStaff->name : '<span class="text-muted">Unassigned</span>';
            })
            ->addColumn('action', function (Lead $lead) {
                return new HtmlString('
                    <div class="btn-group btn-group-sm">
                        <a href="' . route('admin.leads.show', $lead) . '" class="btn btn-outline-primary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="' . route('admin.leads.edit', $lead) . '" class="btn btn-outline-secondary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger delete-lead" data-id="' . $lead->id . '" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ');
            })
            ->editColumn('created_at', function (Lead $lead) {
                return $lead->created_at->format('M d, Y');
            })
            ->rawColumns(['stage_badge', 'assigned_staff_name', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Lead $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Lead $model): QueryBuilder
    {
        return $model->newQuery()->with(['assignedStaff', 'convertedToClient']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('leads-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('
                <"row"<"col-md-6 d-flex justify-content-start"f><"col-sm-12 col-md-6 d-flex align-items-center justify-content-end"lB>>
                <"row"<"col-md-12"tr>>
                <"row"<"col-md-6"i><"col-md-6"p>>
            ')
            ->orderBy(1, 'desc')
            ->language([
                "search" => "",
                "lengthMenu" => "_MENU_",
                "searchPlaceholder" => 'Search leads...'
            ])
            ->buttons(
                Button::make('create')
                    ->className('btn btn-primary')
                    ->text('<i class="bi bi-plus-circle me-1"></i> New Lead')
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . route('admin.leads.create') . '";
                    }'),
                Button::make('reload')
                    ->className('btn btn-secondary')
                    ->text('<i class="bi bi-arrow-clockwise me-1"></i> Reload')
            )
            ->parameters([
                'paging' => true,
                'searching' => true,
                'ordering' => true,
                'info' => true,
                'autoWidth' => false,
                'responsive' => [
                    'details' => [
                        'type' => 'column',
                        'target' => -1
                    ]
                ],
                'pageLength' => 25,
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                'scrollX' => true,
                'scrollCollapse' => true,
                'language' => [
                    'emptyTable' => 'No leads found',
                    'zeroRecords' => 'No matching leads found',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ leads',
                    'infoEmpty' => 'Showing 0 to 0 of 0 leads',
                    'infoFiltered' => '(filtered from _MAX_ total leads)',
                ],
            ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->visible(false),
            Column::make('name')->title('Name')->width('15%'),
            Column::make('company')->title('Company')->width('15%'),
            Column::make('email')->title('Email')->width('15%'),
            Column::make('phone')->title('Phone')->width('10%'),
            Column::make('city')->title('City')->width('10%'),
            Column::make('stage_badge')->title('Stage')->orderable(false)->searchable(false)->width('12%'),
            Column::make('assigned_staff_name')->title('Assigned Staff')->orderable(false)->searchable(false)->width('12%'),
            Column::make('source')->title('Source')->width('10%'),
            Column::make('created_at')->title('Created')->width('10%'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Leads_' . date('YmdHis');
    }
}

