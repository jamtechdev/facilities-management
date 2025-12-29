@extends('layouts.app')

@section('title', 'Users Management')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Users Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    <i class="bi bi-person-gear icon-2-5rem"></i>
                </div>
                <div class="profile-info flex-grow-1">
                    <h1>Users Management</h1>
                    <p>Manage all system users and their roles</p>
                </div>
                @can('create users')
                <div class="profile-header-actions">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-lg px-4 py-2 shadow-lg rounded-pill fw-semibold">
                        <i class="bi bi-plus-circle me-2"></i>Create New User
                    </a>
                </div>
                @endcan
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            {!! $dataTable->table(['class' => 'table table-striped table-hover w-100']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        // jQuery is loaded globally from npm via layout
        $(document).ready(function() {
            // Delete User
            $(document).on('click', '.delete-user', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let url = '{{ \App\Helpers\RouteHelper::url("users.destroy", ":id") }}'.replace(':id', id);

                if (confirm("Are you sure you want to delete this user?")) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                if (typeof showToast !== 'undefined') {
                                    showToast('success', "User deleted successfully");
                                } else if (typeof toastr !== 'undefined') {
                                    toastr.success("User deleted successfully");
                                } else {
                                    alert("User deleted successfully");
                                }
                                $('#users-table').DataTable().ajax.reload(); // reload table
                            } else {
                                if (typeof showToast !== 'undefined') {
                                    showToast('error', "Failed to delete user");
                                } else if (typeof toastr !== 'undefined') {
                                    toastr.error("Failed to delete user");
                                } else {
                                    alert("Failed to delete user");
                                }
                            }
                        },
                        error: function(xhr) {
                            if (typeof showToast !== 'undefined') {
                                showToast('error', "Error deleting user");
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error("Error deleting user");
                            } else {
                                alert("Error deleting user");
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
