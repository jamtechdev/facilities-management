@extends('layouts.app')

@section('title', 'Leads Management')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Leads Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    @if (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    @else
                        <i class="bi bi-person-lines-fill icon-2-5rem"></i>
                    @endif
                </div>
                <div class="profile-info flex-grow-1">
                    <h1>Leads Management</h1>
                    <p>Manage all your leads and track their progress</p>
                </div>
                @can('create leads')
                    <div class="profile-header-actions">
                        <a href="{{ \App\Helpers\RouteHelper::url('leads.create') }}"
                            class="btn btn-light btn-lg px-4 py-2 shadow-lg rounded-pill fw-semibold">
                            <i class="bi bi-plus-circle me-2"></i>Create New Lead
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Leads Table -->
        <div class="row">
            <div class="col-12">
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
    <script>
        // Pass routes to JS for delete operations
        if (typeof window.deleteLeadRoute === 'undefined') {
            window.deleteLeadRoute = '{{ \App\Helpers\RouteHelper::url('leads.destroy', ':id') }}';
        }
        // Pass route for stage update
        if (typeof window.updateStageRoute === 'undefined') {
            window.updateStageRoute = '{{ \App\Helpers\RouteHelper::url('leads.update-stage', ':id') }}';
        }

        document.addEventListener('DOMContentLoaded', function() {

            $(document).off('change', '.stage-select').on('change', '.stage-select', function(e) {
                e.preventDefault();

                const selectElement = $(this);
                const leadId = selectElement.data('lead-id');
                const newStage = selectElement.val();
                const url = window.updateStageRoute.replace(':id', leadId);

                const tableId = 'leads-table';
                const table = window.LaravelDataTables ? window.LaravelDataTables[tableId] : null;

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stage: newStage
                    },
                    success: function(response) {
                        if (response.success) {

                            if (response.redirect) {
                                window.location.href = response.redirect;
                                return;
                            }

                            if (table) {
                                table.ajax.reload(null, false);
                            } else {
                                window.location.reload();
                            }
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON ? xhr.responseJSON.message :
                            'Error updating stage';
                        toastr.error(msg);
                        if (table) table.ajax.reload(null, false);
                    }
                });
            });
        });
    </script>
    {!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
@endpush
