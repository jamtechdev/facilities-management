@extends('layouts.app')

@section('title', 'Leads Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Leads Management</h1>
                    <p class="text-muted">Manage all your leads and track their progress</p>
                </div>
                <a href="{{ route('admin.leads.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create New Lead
                </a>
            </div>
        </div>
    </div>

    <!-- Leads Table -->
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
        // Delete lead handler
        $(document).on('click', '.delete-lead', function(e) {
            e.preventDefault();
            const leadId = $(this).data('id');
            const leadName = $(this).closest('tr').find('td:first').text().trim();

            if (confirm('Are you sure you want to delete lead: ' + leadName + '?')) {
                axios.delete(`/admin/leads/${leadId}`, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .then(function(response) {
                    if (response.data.success) {
                        $('#leads-table').DataTable().ajax.reload(null, false);
                        showAlert('success', response.data.message);
                    }
                })
                .catch(function(error) {
                    showAlert('danger', error.response?.data?.message || 'Failed to delete lead');
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

