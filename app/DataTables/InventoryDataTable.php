<?php

namespace App\DataTables;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InventoryDataTable extends DataTable
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
            ->addColumn('status_badge', function (Inventory $inventory) {
                $statusColors = [
                    'available' => 'success',
                    'assigned' => 'info',
                    'used' => 'warning',
                    'returned' => 'secondary'
                ];
                $color = $statusColors[$inventory->status] ?? 'secondary';
                return new HtmlString('<span class="badge bg-' . $color . '">' . ucfirst($inventory->status) . '</span>');
            })
            ->addColumn('stock_status', function (Inventory $inventory) {
                if ($inventory->isLowStock()) {
                    return new HtmlString('<span class="badge bg-warning">Low Stock</span>');
                }
                return new HtmlString('<span class="badge bg-success">In Stock</span>');
            })
            ->addColumn('assigned_to_name', function (Inventory $inventory) {
                if ($inventory->assignedTo) {
                    if ($inventory->assigned_to_type === 'App\Models\Staff') {
                        return $inventory->assignedTo->name;
                    } else {
                        return $inventory->assignedTo->company_name ?? 'N/A';
                    }
                }
                return '<span class="text-muted">Unassigned</span>';
            })
            ->addColumn('quantity_display', function (Inventory $inventory) {
                $display = number_format($inventory->quantity) . ' ' . ($inventory->unit ?? 'units');
                if ($inventory->isLowStock()) {
                    $display .= ' <span class="badge bg-warning ms-1">Low</span>';
                }
                return new HtmlString($display);
            })
            ->addColumn('unit_cost_formatted', function (Inventory $inventory) {
                return $inventory->unit_cost ? '$' . number_format($inventory->unit_cost, 2) : 'N/A';
            })
            ->addColumn('action', function (Inventory $inventory) {
            $user = auth()->user();
            $actions = '<div class="btn-group btn-group-sm" role="group">';

            // View button - requires view inventory permission
            if ($user->can('view inventory')) {
                $actions .= '<a href="' . route('admin.inventory.show', $inventory) . '" class="btn btn-outline-primary" title="View">
                        <i class="bi bi-eye"></i>
                    </a>';
            }

            // Edit button - redirect to edit page
            if ($user->can('edit inventory')) {
                $actions .= '<a href="' . route('admin.inventory.edit', $inventory) . '" class="btn btn-outline-secondary" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>';
            }

            // Delete button
            if ($user->can('delete inventory')) {
                $actions .= '<button type="button" class="btn btn-outline-danger delete-inventory" data-id="' . $inventory->id . '" title="Delete">
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
            ->editColumn('created_at', function (Inventory $inventory) {
                return $inventory->created_at ? $inventory->created_at->diffForHumans() : 'N/A';
            })
            ->rawColumns(['status_badge', 'stock_status', 'assigned_to_name', 'quantity_display', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Inventory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Inventory $model): QueryBuilder
    {
        return $model->newQuery()->with('assignedTo');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('inventory-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc')
            ->buttons(array_filter([
                auth()->user()->can('create inventory') ? Button::make('create')
                    ->className('btn btn-primary')
                    ->text('<i class="bi bi-plus-circle me-1"></i> New Item')
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . route('admin.inventory.create') . '";
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
                    'emptyTable' => 'No inventory items found',
                    'zeroRecords' => 'No matching inventory items found',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ items',
                    'infoEmpty' => 'Showing 0 to 0 of 0 items',
                    'infoFiltered' => '(filtered from _MAX_ total items)',
                'search' => '',
                'searchPlaceholder' => 'Search inventory...',
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
            Column::make('category')->title('Category')->width('12%'),
            Column::make('quantity_display')->title('Quantity')->orderable(false)->searchable(false)->width('12%'),
            Column::make('unit_cost_formatted')->title('Unit Cost')->orderable(false)->searchable(false)->width('10%'),
            Column::make('status_badge')->title('Status')->orderable(false)->searchable(false)->width('10%'),
            Column::make('stock_status')->title('Stock')->orderable(false)->searchable(false)->width('10%'),
            Column::make('assigned_to_name')->title('Assigned To')->orderable(false)->searchable(false)->width('15%'),
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
        return 'Inventory_' . date('YmdHis');
    }
}
