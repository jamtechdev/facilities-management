@extends('layouts.app')

@section('title', 'Clients Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Clients Management</h1>
                    <p class="text-muted">Manage all your clients and their information</p>
                </div>
                <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create New Client
                </a>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="row">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    {!! $dataTable->table(['class' => 'table table-striped table-hover', 'style' => 'width:100%']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
{!! $dataTable->scripts() !!}

<script>
    $(document).ready(function() {
        // Delete client handler
        $(document).on('click', '.delete-client', function(e) {
            e.preventDefault();
            const clientId = $(this).data('id');
            const clientName = $(this).closest('tr').find('td:first').text().trim();
            
            if (confirm('Are you sure you want to delete client: ' + clientName + '?')) {
                axios.delete(`/admin/clients/${clientId}`, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .then(function(response) {
                    if (response.data.success) {
                        $('#clients-table').DataTable().ajax.reload(null, false);
                        showAlert('success', response.data.message);
                    }
                })
                .catch(function(error) {
                    showAlert('danger', error.response?.data?.message || 'Failed to delete client');
                });
            }
        });

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('.container-fluid').prepend(alertHtml);
            setTimeout(() => $('.alert').fadeOut(), 5000);
        }
    });
</script>
@endpush

