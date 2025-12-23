<?php

namespace App\DataTables;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StaffDataTable extends DataTable
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
            ->addIndexColumn()
            ->addColumn('status_badge', function (Staff $staff) {
                $badge = $staff->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
                return new HtmlString($badge);
            })
            ->addColumn('hourly_rate_formatted', function (Staff $staff) {
                return '£' . number_format($staff->hourly_rate ?? 0, 2);
            })
            ->addColumn('assigned_clients_count', function (Staff $staff) {
                $count = $staff->clients()->wherePivot('is_active', true)->count();
                return $count > 0 ? $count : '<span class="text-muted">None</span>';
            })
            ->addColumn('action', function (Staff $staff) {
            $user = auth()->user();
            $actions = '<div class="btn-group btn-group-sm" role="group">';

            // View button - requires view staff details permission
            if ($user->can('view staff details')) {
                $actions .= '<a href="' . route('admin.staff.show', $staff) . '" class="btn btn-outline-primary" title="View">
                        <i class="bi bi-eye"></i>
                    </a>';
            }

            // Edit button
            if ($user->can('edit staff')) {
                $actions .= '<a href="' . route('admin.staff.edit', $staff) . '" class="btn btn-outline-secondary" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>';
            }

            // Delete button
            if ($user->can('delete staff')) {
                $actions .= '<button type="button" class="btn btn-outline-danger delete-staff" data-id="' . $staff->id . '" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>';
            }

            $actions .= '</div>';
            
            // If no actions available, return a dash
            if (strlen($actions) <= strlen('<div class="btn-group btn-group-sm" role="group"></div>')) {
                return new HtmlString('<span class="text-muted">-</span>');
            }
            
            return new HtmlString($actions);
            })
            ->editColumn('created_at', function (Staff $staff) {
                return $staff->created_at ? $staff->created_at->diffForHumans() : 'N/A';
            })
            ->rawColumns(['status_badge', 'assigned_clients_count', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Staff $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Staff $model): QueryBuilder
    {
        return $model->newQuery()->with(['user', 'clients']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('staff-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc')
            ->buttons(array_filter([
                auth()->user()->can('create staff') ? Button::make('create')
                    ->className('btn btn-primary')
                    ->text('<i class="bi bi-plus-circle me-1"></i> New Staff')
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . route('admin.staff.create') . '";
                    }') : null,
                Button::make('reload')
                    ->className('btn btn-secondary')
                    ->text('<i class="bi bi-arrow-clockwise me-1"></i> Reload')
        ]))
            ->parameters([
                'paging' => true,
                'searching' => true,
                'ordering' => true,
                'info' => true,
                'autoWidth' => false,
            'responsive' => true,
                'pageLength' => 10,
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                'scrollX' => true,
                'scrollCollapse' => true,
                'language' => [
                    'emptyTable' => 'No staff found',
                    'zeroRecords' => 'No matching staff found',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ staff',
                    'infoEmpty' => 'Showing 0 to 0 of 0 staff',
                    'infoFiltered' => '(filtered from _MAX_ total staff)',
                'search' => '',
                'searchPlaceholder' => 'Search staff...',
                'lengthMenu' => 'Show _MENU_ entries',
                'paginate' => [
                    'first' => 'First',
                    'last' => 'Last',
                    'next' => 'Next',
                    'previous' => 'Previous'
                ]
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
            Column::make('DT_RowIndex')->title('SR No')->orderable(false)->searchable(false)->width(60)->addClass('text-center'),
            Column::make('id')->visible(false),
            Column::make('name')->title('Name')->width('20%'),
            Column::make('email')->title('Email')->width('20%'),
            Column::make('mobile')->title('Mobile')->width('15%'),
            Column::make('hourly_rate_formatted')->title('Hourly Rate')->orderable(false)->searchable(false)->width('12%'),
            Column::make('status_badge')->title('Status')->orderable(false)->searchable(false)->width('10%'),
            Column::make('assigned_clients_count')->title('Clients')->orderable(false)->searchable(false)->width('10%'),
            Column::make('created_at')->title('Created')->width('13%'),
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
        return 'Staff_' . date('YmdHis');
    }
}

