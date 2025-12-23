/**
 * Centralized DataTables Event Handlers
 * Handles all delete actions and alerts for DataTables
 */

(function() {
    'use strict';

    // Wait for jQuery and DOM
    function initDataTablesHandlers() {
        if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
            setTimeout(initDataTablesHandlers, 100);
            return;
        }

        const $ = window.jQuery;

        $(document).ready(function() {
            // Generic delete handler for all DataTables
            $(document).on('click', '.delete-lead, .delete-client, .delete-staff, .delete-invoice', function(e) {
                e.preventDefault();

                const $button = $(this);
                let entityType, url;

                if ($button.hasClass('delete-lead')) {
                    entityType = 'lead';
                    url = `/admin/leads/${$button.data('id')}`;
                } else if ($button.hasClass('delete-client')) {
                    entityType = 'client';
                    url = `/admin/clients/${$button.data('id')}`;
                } else if ($button.hasClass('delete-staff')) {
                    entityType = 'staff';
                    url = `/admin/staff/${$button.data('id')}`;
                } else if ($button.hasClass('delete-invoice')) {
                    entityType = 'invoice';
                    url = `/admin/invoices/${$button.data('id')}`;
                } else {
                    return; // Unknown entity type
                }

                const entityId = $button.data('id');
                const entityName = $button.closest('tr').find('td:first').text().trim() || entityType;
                const tableId = $button.closest('.dataTables_wrapper').find('table').attr('id');

                if (confirm(`Are you sure you want to delete ${entityType}: ${entityName}?`)) {

                    axios.delete(url, {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .then(function(response) {
                        if (response.data.success) {
                            // Reload DataTable if it exists
                            if (tableId && typeof $.fn.DataTable !== 'undefined') {
                                $(`#${tableId}`).DataTable().ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                            showAlert('success', response.data.message || `${entityType} deleted successfully`);
                        }
                    })
                    .catch(function(error) {
                        showAlert('danger', error.response?.data?.message || `Failed to delete ${entityType}`);
                    });
                }
            });

            // Generic alert function - using toastr
            window.showAlert = function(type, message) {
                if (typeof showToast !== 'undefined') {
                    showToast(type, message);
                } else if (typeof toastr !== 'undefined') {
                    const toastType = type === 'danger' ? 'error' : type;
                    toastr[toastType](message);
                } else {
                    alert(message);
                }
            };
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDataTablesHandlers);
    } else {
        initDataTablesHandlers();
    }
})();

