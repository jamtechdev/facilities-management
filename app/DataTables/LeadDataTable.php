<?php

namespace App\DataTables;

use App\Models\Lead;
use App\Helpers\RouteHelper;
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
            ->addIndexColumn()
            ->addColumn('stage_badge', function (Lead $lead) {
                $user = auth()->user();
                $canEditStage = $user->can('view roles'); // SuperAdmin can edit directly

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

                // If SuperAdmin, show editable dropdown
                if ($canEditStage) {
                    $options = '';
                    foreach ($stageLabels as $stage => $label) {
                        $selected = $lead->stage === $stage ? 'selected' : '';
                        $options .= '<option value="' . $stage . '" ' . $selected . '>' . $label . '</option>';
                    }
                    return new HtmlString('<select class="form-select form-select-sm stage-select" data-lead-id="' . $lead->id . '" style="min-width: 120px;">' . $options . '</select>');
                }

                // Otherwise show badge
                $color = $stageColors[$lead->stage] ?? 'secondary';
                $label = $stageLabels[$lead->stage] ?? $lead->stage;
                return new HtmlString('<span class="badge bg-' . $color . '">' . $label . '</span>');
            })
            ->addColumn('assigned_staff_name', function (Lead $lead) {
                return $lead->assignedStaff ? $lead->assignedStaff->name : '<span class="text-muted">Unassigned</span>';
            })
            ->addColumn('action', function (Lead $lead) {
            $user = auth()->user();
            $actions = '<div class="btn-group btn-group-sm" role="group">';

            // View button - requires view lead details permission
            if ($user->can('view lead details')) {
                $actions .= '<a href="' . RouteHelper::url('leads.show', $lead) . '" class="btn btn-outline-primary" title="View">
                        <i class="bi bi-eye"></i>
                    </a>';
            }

            // Edit button
            if ($user->can('edit leads')) {
                $actions .= '<a href="' . RouteHelper::url('leads.edit', $lead) . '" class="btn btn-outline-secondary" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>';
            }

            // Delete button
            if ($user->can('delete leads')) {
                $actions .= '<button type="button" class="btn btn-outline-danger delete-lead" data-id="' . $lead->id . '" title="Delete">
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
            ->editColumn('created_at', function (Lead $lead) {
                return $lead->created_at ? $lead->created_at->diffForHumans() : 'N/A';
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
        return $model->newQuery()
        ->with(['assignedStaff', 'convertedToClient'])
        ->where('stage', '!=', 'qualified');
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
            ->orderBy(1, 'desc')
            ->buttons(array_filter([
                auth()->user()->can('create leads') ? Button::make('create')
                    ->className('btn btn-primary')
                    ->text('<i class="bi bi-plus-circle me-1"></i> New Lead')
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . RouteHelper::url('leads.create') . '";
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
                    'emptyTable' => 'No leads found',
                    'zeroRecords' => 'No matching leads found',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ leads',
                    'infoEmpty' => 'Showing 0 to 0 of 0 leads',
                    'infoFiltered' => '(filtered from _MAX_ total leads)',
                'search' => '',
                'searchPlaceholder' => 'Search leads...',
                'lengthMenu' => 'Show _MENU_ entries',
                'paginate' => [
                    'first' => '«',
                    'last' => '»',
                    'next' => '›',
                    'previous' => '‹'
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
