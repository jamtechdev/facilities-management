<?php

namespace App\DataTables;

use App\Models\Client;
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
                return new HtmlString('
                    <div class="btn-group btn-group-sm">
                        <a href="' . route('admin.clients.show', $client) . '" class="btn btn-outline-primary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="' . route('admin.clients.edit', $client) . '" class="btn btn-outline-secondary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger delete-client" data-id="' . $client->id . '" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ');
            })
            ->editColumn('created_at', function (Client $client) {
                return $client->created_at->format('M d, Y');
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
            ->dom('
                <"row"<"col-md-6 d-flex justify-content-start"f><"col-sm-12 col-md-6 d-flex align-items-center justify-content-end"lB>>
                <"row"<"col-md-12"tr>>
                <"row"<"col-md-6"i><"col-md-6"p>>
            ')
            ->orderBy(1, 'desc')
            ->language([
                "search" => "",
                "lengthMenu" => "_MENU_",
                "searchPlaceholder" => 'Search clients...'
            ])
            ->buttons(
                Button::make('create')
                    ->className('btn btn-primary')
                    ->text('<i class="bi bi-plus-circle me-1"></i> New Client')
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . route('admin.clients.create') . '";
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
                'responsive' => true,
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

