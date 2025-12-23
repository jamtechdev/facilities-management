<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
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
            ->addColumn('roles', function (User $user) {
                $rolesHtml = '';
                foreach ($user->roles as $role) {
                $badgeClass = $role->name === 'SuperAdmin' ? 'danger' : 'primary';
                    $rolesHtml .= '<span class="badge bg-' . $badgeClass . ' me-1">' . $role->name . '</span>';
                }
                return new HtmlString($rolesHtml ?: '<span class="text-muted">No roles</span>');
            })
            // ->addColumn('action', function (User $user) {
            //     return '<div class="dropdown">
            //                 <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            //                     <i class="bi bi-three-dots-vertical"></i>
            //                 </button>
            //                 <div class="dropdown-menu">
            //                     <a class="dropdown-item view-user" href="' . route('admin.users.show', $user->id) . '" data-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#viewUserModal">
            //                         <i class="bi bi-eye me-1"></i> View
            //                     </a>
            //                     <a class="dropdown-item" href="' . route('admin.users.edit', $user->id) . '" data-id="' . $user->id . '">
            //                         <i class="bi bi-pencil me-1"></i> Edit
            //                     </a>
            //                     <div class="dropdown-divider"></div>
            //                     <a class="dropdown-item text-danger delete-user" href="' . route('admin.users.destroy', $user->id) . '" data-id="' . $user->id . '">
            //                         <i class="bi bi-trash me-1"></i> Delete
            //                     </a>
            //                 </div>
            //             </div>';
            // })
            ->addColumn('action', function (User $user) {
            $currentUser = auth()->user();
            $actions = '<div class="btn-group btn-group-sm" role="group">';

            // View button - requires view user details permission
            if ($currentUser->can('view user details')) {
                $actions .= '<a href="' . route('admin.users.show', $user->id) . '" class="btn btn-outline-primary view-user" data-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#viewUserModal" title="View">
                        <i class="bi bi-eye"></i>
                    </a>';
            }

            // Edit button
            if ($currentUser->can('edit users') && $user->id !== $currentUser->id) {
                $actions .= '<a href="' . route('admin.users.edit', $user->id) . '" class="btn btn-outline-secondary" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>';
            }

            // Delete button
            if ($currentUser->can('delete users') && $user->id !== $currentUser->id) {
                $actions .= '<button type="button" class="btn btn-outline-danger delete-user" data-id="' . $user->id . '" title="Delete">
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


            ->editColumn('created_at', function (User $user) {
                return $user->created_at ? $user->created_at->diffForHumans() : 'N/A';
            })
            ->rawColumns(['roles', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        $query = $model->newQuery()->with('roles');
        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->buttons(array_filter([
                auth()->user()->can('create users') ? Button::make()
                    ->className('btn btn-primary')
                    ->text('<i class="bi bi-plus-circle"></i> New User')
                    ->action('function(e, dt, node, config) {
                        let url = "' . route('admin.users.create') . '";
                        console.log("Button clicked. Redirecting to: " + url);
                        window.location.href = url;
                    }') : null,
        ]))
            ->parameters([
                'paging' => true,
            'searching' => true,
            'ordering' => true,
            'info' => true,
            'autoWidth' => false,
            'responsive' => true,
                'lengthMenu' => [
                    [5, 10, 25, 50, -1],
                    ['5', '10', '25', '50', 'Show all']
                ],
            'pageLength' => 10,
                'scrollX' => true,
            'scrollCollapse' => true,
                'language' => [
                    'emptyTable' => 'No users found',
                    'zeroRecords' => 'No matching users found',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ users',
                    'infoEmpty' => 'Showing 0 to 0 of 0 users',
                    'infoFiltered' => '(filtered from _MAX_ total users)',
                'search' => '',
                'searchPlaceholder' => 'Search users...',
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
            Column::make('id')->visible(false)->style('width:200px'),
            Column::make('name')->title('Name')->style('width:200px'),
            Column::make('email')->title('Email')->style('width:200px'),
            Column::make('roles')
                ->title('Roles')
                ->orderable(false)
                ->searchable(false)
                ->style('width:150px'),
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
        return 'Users_' . date('YmdHis');
    }
}
