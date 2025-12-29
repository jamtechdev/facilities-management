<?php

namespace App\DataTables;

use App\Models\Client;
use App\Helpers\RouteHelper;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ClientDataTable extends DataTable
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
            ->addColumn('status_badge', function (Client $client) {
            $badge = $client->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
                return new HtmlString($badge);
            })
            ->addColumn('billing_frequency_badge', function (Client $client) {
                $colors = [
                    'weekly' => 'primary',
                    'monthly' => 'info',
                    'bi-weekly' => 'success',
                    'quarterly' => 'warning'
                ];
                $color = $colors[$client->billing_frequency] ?? 'secondary';
                return new HtmlString('<span class="badge bg-' . $color . '">' . ucfirst($client->billing_frequency ?? 'N/A') . '</span>');
            })
            ->addColumn('assigned_staff_count', function (Client $client) {
                $count = $client->staff()->wherePivot('is_active', true)->count();
                return $count > 0 ? $count : '<span class="text-muted">None</span>';
            })
            ->addColumn('action', function (Client $client) {
            $user = auth()->user();
            $actions = '<div class="btn-group btn-group-sm" role="group">';

            // View button - requires view client details permission
            if ($user->can('view client details')) {
                $actions .= '<a href="' . RouteHelper::url('clients.show', $client) . '" class="btn btn-outline-primary" title="View">
                        <i class="bi bi-eye"></i>
                    </a>';
            }

            // Edit button
            if ($user->can('edit clients')) {
                $actions .= '<a href="' . RouteHelper::url('clients.edit', $client) . '" class="btn btn-outline-secondary" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>';
            }

            // Delete button
            if ($user->can('delete clients')) {
                $actions .= '<button type="button" class="btn btn-outline-danger delete-client" data-id="' . $client->id . '" title="Delete">
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
            ->editColumn('created_at', function (Client $client) {
                return $client->created_at ? $client->created_at->diffForHumans() : 'N/A';
            })
            ->rawColumns(['status_badge', 'billing_frequency_badge', 'assigned_staff_count', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Client $model): QueryBuilder
    {
        return $model->newQuery()->with(['user', 'lead', 'staff']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('clients-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc')
            ->buttons(array_filter([
                auth()->user()->can('create clients') ? Button::make('create')
                    ->className('btn btn-primary')
                    ->text('<i class="bi bi-plus-circle me-1"></i> New Client')
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . RouteHelper::url('clients.create') . '";
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
                    'emptyTable' => 'No clients found',
                    'zeroRecords' => 'No matching clients found',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ clients',
                    'infoEmpty' => 'Showing 0 to 0 of 0 clients',
                    'infoFiltered' => '(filtered from _MAX_ total clients)',
                'search' => '',
                'searchPlaceholder' => 'Search clients...',
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
            Column::make('company_name')->title('Company Name')->width('20%'),
            Column::make('contact_person')->title('Contact Person')->width('15%'),
            Column::make('email')->title('Email')->width('15%'),
            Column::make('phone')->title('Phone')->width('12%'),
            Column::make('city')->title('City')->width('10%'),
            Column::make('status_badge')->title('Status')->orderable(false)->searchable(false)->width('8%'),
            Column::make('billing_frequency_badge')->title('Billing')->orderable(false)->searchable(false)->width('10%'),
            Column::make('assigned_staff_count')->title('Staff')->orderable(false)->searchable(false)->width('8%'),
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
        return 'Clients_' . date('YmdHis');
    }
}
