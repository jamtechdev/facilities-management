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
            ->editColumn('checkbox', function (User $user) {
                return new HtmlString('<input type="checkbox" name="selected_user[]" class="row-checkbox" value="' . $user->id . '" />');
            })
            ->addColumn('roles', function (User $user) {
                $rolesHtml = '';
                foreach ($user->roles as $role) {
                    $badgeClass = $role->name === 'Admin' ? 'danger' : 'primary';
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
                $canEditDelete = true;

                if ($currentUser->hasRole('Admin')) {
                    // Admin cannot edit/delete SuperAdmin or self
                    if ($user->hasRole('SuperAdmin') || $user->id === $currentUser->id) {
                        $canEditDelete = false;
                    }
                }

                $actions = '<div class="dropdown">
        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item view-user" href="' . route('admin.users.show', $user->id) . '" data-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#viewUserModal">
                <i class="bi bi-eye me-1"></i> View
            </a>';

                if ($canEditDelete) {
                    $actions .= '<a class="dropdown-item" href="' . route('admin.users.edit', $user->id) . '">
                        <i class="bi bi-pencil me-1"></i> Edit
                     </a>
                     <a class="dropdown-item text-danger delete-user" href="' . route('admin.users.destroy', $user->id) . '">
                        <i class="bi bi-trash me-1"></i> Delete
                     </a>';
                }

                $actions .= '</div></div>';

                return $actions;
            })


            ->addColumn('created_at', function (User $user): HtmlString|string {
                if ($user->created_at === null) {
                    return 'n/A';
                }
                $dates = 'Created: ' . $user->created_at->diffForHumans() . '<br><hr/>';
                if ($user->updated_at === null) {
                    $dates .= 'Updated: n/A';
                } else {
                    $dates .= 'Updated: ' . $user->updated_at->diffForHumans();
                }
                return new HtmlString($dates);
            })
            ->rawColumns(['checkbox', 'roles', 'action', 'created_at']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->with('roles');

        if (auth()->user()->hasRole('Admin')) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'SuperAdmin');
            });
        }

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
            ->dom('
                <"row"<"col-md-6 d-flex justify-content-start"f><"col-sm-12 col-md-6 d-flex align-items-center justify-content-end"lB>>
                <"row"<"col-md-12"tr>>
                <"row"<"col-md-6"i><"col-md-6"p>>
            ')
            ->orderBy(1)
            ->language([
                "search" => "",
                "lengthMenu" => "_MENU_",
                "searchPlaceholder" => 'Search Users'
            ])
            ->buttons(
                Button::make()
                    ->className('btn btn-primary')
                    ->text('<i class="bi bi-plus-circle"></i> New User')
                    ->action('function(e, dt, node, config) {
                        let url = "' . route('admin.users.create') . '";
                        console.log("Button clicked. Redirecting to: " + url);
                        window.location.href = url;
                    }'),
            )
            ->parameters([
                'paging' => true,
                'lengthMenu' => [
                    [5, 10, 25, 50, -1],
                    ['5', '10', '25', '50', 'Show all']
                ],
                'scrollY' => true,
                'scrollX' => true,
                'scrollCollapse' => true,
                'responsive' => [
                    'details' => [
                        'type' => 'column',
                        'target' => -1
                    ]
                ],
                'language' => [
                    'emptyTable' => 'No users found',
                    'zeroRecords' => 'No matching users found',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ users',
                    'infoEmpty' => 'Showing 0 to 0 of 0 users',
                    'infoFiltered' => '(filtered from _MAX_ total users)',
                ],
                'initComplete' => 'function () {
                    var selectedIds = [];
                    function logSelectedIds() {
                        console.log(selectedIds);
                    }
                    $("#check-all").on("change", function () {
                        var isChecked = $(this).prop("checked");
                        $(".row-checkbox").prop("checked", isChecked);
                        if (isChecked) {
                            selectedIds = [];
                            $(".row-checkbox:checked").each(function() {
                                selectedIds.push($(this).val());
                            });
                        } else {
                            selectedIds = [];
                        }
                        logSelectedIds();
                    });
                    $(document).on("change", ".row-checkbox", function () {
                        var isChecked = $(this).prop("checked");
                        var rowId = $(this).val();
                        if (isChecked) {
                            selectedIds.push(rowId);
                        } else {
                            var index = selectedIds.indexOf(rowId);
                            if (index !== -1) {
                                selectedIds.splice(index, 1);
                            }
                        }
                        logSelectedIds();
                        var allChecked = $(".row-checkbox:checked").length === $(".row-checkbox").length;
                        $("#check-all").prop("checked", allChecked);
                    });
                }',
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
            Column::make('id')->visible(false)->style('width:200px'),
            Column::make('checkbox')
                ->title('<input type="checkbox" id="check-all"/>')
                ->orderable(false)
                ->searchable(false)
                ->width(10)
                ->style('width:50px'),
            Column::make('name')->title('Name')->style('width:200px'),
            Column::make('email')->title('Email')->style('width:200px'),
            Column::make('roles')
                ->title('Roles')
                ->orderable(false)
                ->searchable(false)
                ->style('width:150px'),
            Column::make('created_at')->title('Created At')->style('width:200px'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center')
                ->style('width:100px'),
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
