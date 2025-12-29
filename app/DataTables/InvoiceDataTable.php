<?php

namespace App\DataTables;

use App\Models\Invoice;
use App\Helpers\RouteHelper;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InvoiceDataTable extends DataTable
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
            ->addColumn('status_badge', function (Invoice $invoice) {
                $statusColors = [
                    'draft' => 'secondary',
                    'sent' => 'info',
                    'paid' => 'success',
                    'unpaid' => 'warning',
                    'overdue' => 'danger'
                ];
                $statusLabels = [
                    'draft' => 'Draft',
                    'sent' => 'Sent',
                    'paid' => 'Paid',
                    'unpaid' => 'Unpaid',
                    'overdue' => 'Overdue'
                ];
                $color = $statusColors[$invoice->status] ?? 'secondary';
                $label = $statusLabels[$invoice->status] ?? $invoice->status;
                return new HtmlString('<span class="badge bg-' . $color . '">' . $label . '</span>');
            })
            ->addColumn('client_name', function (Invoice $invoice) {
                return $invoice->client ? $invoice->client->company_name : 'N/A';
            })
            ->addColumn('action', function (Invoice $invoice) {
            $user = auth()->user();
            $actions = '<div class="btn-group btn-group-sm" role="group">';

            // View button - requires view invoice details permission
            if ($user->can('view invoice details')) {
                $actions .= '<a href="' . RouteHelper::url('invoices.show', $invoice) . '" class="btn btn-outline-primary" title="View">
                        <i class="bi bi-eye"></i>
                    </a>';
            }

            // Download button
            if ($user->can('view invoices')) {
                $actions .= '<a href="' . RouteHelper::url('invoices.download', $invoice) . '" class="btn btn-outline-info" title="Download PDF">
                        <i class="bi bi-download"></i>
                    </a>';
            }

            // Delete button
            if ($user->can('delete invoices')) {
                $actions .= '<button type="button" class="btn btn-outline-danger delete-invoice" data-id="' . $invoice->id . '" title="Delete">
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
            ->editColumn('total_amount', function (Invoice $invoice) {
                return '$' . number_format($invoice->total_amount, 2);
            })
            ->editColumn('billing_period_start', function (Invoice $invoice) {
                return $invoice->billing_period_start ? $invoice->billing_period_start->format('M d, Y') : 'N/A';
            })
            ->editColumn('billing_period_end', function (Invoice $invoice) {
                return $invoice->billing_period_end ? $invoice->billing_period_end->format('M d, Y') : 'N/A';
            })
            ->editColumn('created_at', function (Invoice $invoice) {
                return $invoice->created_at ? $invoice->created_at->diffForHumans() : 'N/A';
            })
            ->rawColumns(['status_badge', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Invoice $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Invoice $model): QueryBuilder
    {
        return $model->newQuery()->with('client');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('invoices-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc')
            ->buttons(array_filter([
                auth()->user()->can('create invoices') ? Button::make('create')
                    ->className('btn btn-primary')
                    ->text('<i class="bi bi-plus-circle me-1"></i> New Invoice')
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . RouteHelper::url('invoices.create') . '";
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
                    'emptyTable' => 'No invoices found',
                    'zeroRecords' => 'No matching invoices found',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ invoices',
                    'infoEmpty' => 'Showing 0 to 0 of 0 invoices',
                    'infoFiltered' => '(filtered from _MAX_ total invoices)',
                'search' => '',
                'searchPlaceholder' => 'Search invoices...',
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
            Column::make('invoice_number')->title('Invoice #')->width('12%'),
            Column::make('client_name')->title('Client')->orderable(false)->searchable(false)->width('15%'),
            Column::make('billing_period_start')->title('Period Start')->width('12%'),
            Column::make('billing_period_end')->title('Period End')->width('12%'),
            Column::make('total_hours')->title('Hours')->width('8%'),
            Column::make('total_amount')->title('Amount')->width('10%'),
            Column::make('status_badge')->title('Status')->orderable(false)->searchable(false)->width('10%'),
            Column::make('created_at')->title('Created')->width('10%'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
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
        return 'Invoices_' . date('YmdHis');
    }
}
