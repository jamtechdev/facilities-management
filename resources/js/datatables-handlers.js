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
            $(document).on('click', '.delete-lead, .delete-client, .delete-staff, .delete-invoice, .delete-inventory, .delete-user', function(e) {
                e.preventDefault();

                const $button = $(this);
                let entityType, url;

                // Get route from data attribute or window object
                if ($button.hasClass('delete-lead')) {
                    entityType = 'lead';
                    url = $button.data('delete-url') || window.deleteLeadRoute?.replace(':id', $button.data('id')) || `/admin/leads/${$button.data('id')}`;
                } else if ($button.hasClass('delete-client')) {
                    entityType = 'client';
                    url = $button.data('delete-url') || window.deleteClientRoute?.replace(':id', $button.data('id')) || `/admin/clients/${$button.data('id')}`;
                } else if ($button.hasClass('delete-staff')) {
                    entityType = 'staff';
                    url = $button.data('delete-url') || window.deleteStaffRoute?.replace(':id', $button.data('id')) || `/admin/staff/${$button.data('id')}`;
                } else if ($button.hasClass('delete-invoice')) {
                    entityType = 'invoice';
                    const invoiceId = $button.data('id');
                    // Get prefix from current URL
                    const pathParts = window.location.pathname.split('/').filter(p => p);
                    const prefix = pathParts[0] || 'admin';
                    url = $button.data('delete-url') || window.deleteInvoiceRoute?.replace(':id', invoiceId) || `/${prefix}/invoices/${invoiceId}`;
                } else if ($button.hasClass('delete-inventory')) {
                    entityType = 'inventory item';
                    const inventoryId = $button.data('id');
                    // Get prefix from current URL
                    const pathParts = window.location.pathname.split('/').filter(p => p);
                    const prefix = pathParts[0] || 'admin';
                    url = $button.data('delete-url') || window.deleteInventoryRoute?.replace(':id', inventoryId) || `/${prefix}/inventory/${inventoryId}`;
                } else if ($button.hasClass('delete-user')) {
                    entityType = 'user';
                    const userId = $button.data('id');
                    // Get prefix from current URL
                    const pathParts = window.location.pathname.split('/').filter(p => p);
                    const prefix = pathParts[0] || 'admin';
                    url = $button.data('delete-url') || window.deleteUserRoute?.replace(':id', userId) || `/${prefix}/users/${userId}`;
                } else {
                    return; // Unknown entity type
                }

                const entityId = $button.data('id');
                const entityName = $button.closest('tr').find('td:first').text().trim() || entityType;
                const tableId = $button.closest('.dataTables_wrapper').find('table').attr('id');

                if (confirm(`Are you sure you want to delete ${entityType}: ${entityName}?`)) {
                    // Replace :id placeholder if URL template is used
                    url = url.replace(':id', entityId);

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

            // Stage change handler for leads table (SuperAdmin only)
            $(document).on('change', '.stage-select', function() {
                const $select = $(this);
                const leadId = $select.data('lead-id');
                const newStage = $select.val();
                const originalValue = $select.data('original-value') || $select.find('option[selected]').val();

                // Store original value if not stored
                if (!$select.data('original-value')) {
                    $select.data('original-value', originalValue);
                }

                // Disable select while processing
                $select.prop('disabled', true);
                const originalHtml = $select.html();

                // Get route from window object or construct from current URL
                let updateUrl;
                if (typeof window.updateStageRoute !== 'undefined') {
                    updateUrl = window.updateStageRoute.replace(':id', leadId);
                } else {
                    // Fallback: construct from current URL
                    const baseUrl = window.location.origin;
                    const pathParts = window.location.pathname.split('/').filter(p => p);
                    const prefix = pathParts[0] || 'admin'; // admin or superadmin
                    updateUrl = `${baseUrl}/${prefix}/leads/${leadId}/update-stage`;
                }

                axios.post(updateUrl, {
                    stage: newStage
                }, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(function(response) {
                    if (response.data.success) {
                        // Update original value
                        $select.data('original-value', newStage);
                        showAlert('success', response.data.message || 'Lead stage updated successfully');

                        // Only reload DataTable if lead is NOT converted
                        // If qualified, the convert button will appear on the lead show page
                        if (!response.data.is_converted) {
                            const tableId = $select.closest('.dataTables_wrapper').find('table').attr('id');
                            if (tableId && typeof $.fn.DataTable !== 'undefined') {
                                $(`#${tableId}`).DataTable().ajax.reload(null, false);
                            }
                        } else {
                            // If converted, just reload to show converted status
                            const tableId = $select.closest('.dataTables_wrapper').find('table').attr('id');
                            if (tableId && typeof $.fn.DataTable !== 'undefined') {
                                $(`#${tableId}`).DataTable().ajax.reload(null, false);
                            }
                        }
                    }
                })
                .catch(function(error) {
                    // Revert to original value
                    $select.val(originalValue);
                    showAlert('danger', error.response?.data?.message || 'Failed to update lead stage');
                })
                .finally(function() {
                    $select.prop('disabled', false);
                });
            });

            // Row click handlers - make table rows clickable to open Show page
            // Handle clicks on table rows (but not on action buttons)
            $(document).on('click', '#leads-table tbody tr, #clients-table tbody tr, #staff-table tbody tr, #invoices-table tbody tr', function(e) {
                // Don't trigger if clicking on buttons, links, or select dropdowns
                if ($(e.target).closest('button, a, .btn, .delete-lead, .delete-client, .delete-staff, .delete-invoice, .stage-select, select, .form-select').length > 0) {
                    return;
                }

                const $row = $(this);
                const tableId = $row.closest('table').attr('id');

                // Get the ID from the row's ID attribute (setRowId('id') in DataTables)
                // The row ID is set to the entity ID by DataTables
                let entityId = $row.attr('id');

                // Fallback: try to get from hidden ID column (usually column index 1)
                if (!entityId) {
                    const idCell = $row.find('td').eq(1);
                    if (idCell.length && idCell.text().trim()) {
                        entityId = idCell.text().trim();
                    }
                }

                if (!entityId) return;

                // Determine route based on table ID
                let showUrl = '';
                const pathParts = window.location.pathname.split('/').filter(p => p);
                const prefix = pathParts[0] || 'admin'; // admin or superadmin

                if (tableId === 'leads-table') {
                    showUrl = `/${prefix}/leads/${entityId}`;
                } else if (tableId === 'clients-table') {
                    showUrl = `/${prefix}/clients/${entityId}`;
                } else if (tableId === 'staff-table') {
                    showUrl = `/${prefix}/staff/${entityId}`;
                } else if (tableId === 'invoices-table') {
                    showUrl = `/${prefix}/invoices/${entityId}`;
                }

                if (showUrl) {
                    window.location.href = showUrl;
                }
            });

            // Add cursor pointer style to table rows
            $(document).on('draw.dt', '#leads-table, #clients-table, #staff-table, #invoices-table', function() {
                $(this).find('tbody tr').css('cursor', 'pointer');
            });

            // Generic alert function - using toastr
            window.showAlert = function(type, message) {
                if (typeof window.showToast !== 'undefined') {
                    window.showToast(type, message);
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
