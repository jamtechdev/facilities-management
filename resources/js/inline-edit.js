/**
 * Inline Editing Handler for Show Pages
 * Handles inline editing of fields on Lead, Client, Staff, and Invoice Show pages
 */

(function() {
    'use strict';

    function initInlineEdit() {
        if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
            setTimeout(initInlineEdit, 100);
            return;
        }

        const $ = window.jQuery;

        $(document).ready(function() {
            // Edit button click
            $(document).on('click', '.btn-edit-field', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $card = $(this).closest('.editable-info-card');
                const $display = $card.find('.field-display');
                const $edit = $card.find('.field-edit');
                const $actions = $card.find('.field-actions');

                // Hide display, show edit
                $display.addClass('d-none');
                $edit.removeClass('d-none');
                $actions.removeClass('d-none');

                // Focus on input
                if ($edit.is('input, textarea')) {
                    $edit.focus().select();
                }
            });

            // Cancel button click
            $(document).on('click', '.btn-cancel-field', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $card = $(this).closest('.editable-info-card');
                const $display = $card.find('.field-display');
                const $edit = $card.find('.field-edit');
                const $actions = $card.find('.field-actions');

                // Reset value
                const originalValue = $edit.data('original-value') || $display.text().trim();
                if ($edit.is('input, textarea')) {
                    $edit.val(originalValue);
                } else if ($edit.is('select')) {
                    $edit.val($edit.data('original-value') || '');
                }

                // Hide edit, show display
                $edit.addClass('d-none');
                $actions.addClass('d-none');
                $display.removeClass('d-none');
            });

            // Save button click
            $(document).on('click', '.btn-save-field', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $card = $(this).closest('.editable-info-card');
                const $display = $card.find('.field-display');
                const $edit = $card.find('.field-edit');
                const $actions = $card.find('.field-actions');
                const $btnSave = $(this);

                const field = $card.data('field');
                const entityType = $card.data('entity-type');
                const entityId = $card.data('entity-id');
                const fieldType = $card.data('field-type');

                // Get new value
                let newValue = '';
                if ($edit.is('input, textarea')) {
                    newValue = $edit.val().trim();
                    // For number fields, ensure it's a valid number
                    if (fieldType === 'number') {
                        newValue = parseFloat(newValue) || 0;
                    }
                } else if ($edit.is('select')) {
                    newValue = $edit.val();
                }

                // Disable save button
                $btnSave.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Saving...');

                // Determine update URL based on current route
                const pathParts = window.location.pathname.split('/').filter(p => p);
                let prefix = 'admin';

                // Check if we're in superadmin routes
                if (pathParts[0] === 'superadmin' || pathParts[0] === 'admin') {
                    prefix = pathParts[0];
                }

                // Build update URL
                const updateUrl = `/${prefix}/${entityType}/${entityId}`;

                // Prepare data
                const data = {};
                data[field] = newValue;

                // Send update request
                axios.put(updateUrl, data, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) {
                    if (response.data.success) {
                        // Update display value
                        if (fieldType === 'select') {
                            const selectedOption = $edit.find('option:selected').text();
                            $display.text(selectedOption || '-');
                        } else if (fieldType === 'number' && field === 'hourly_rate') {
                            $display.text('$' + parseFloat(newValue || 0).toFixed(2));
                        } else if (fieldType === 'number' && field === 'tax') {
                            $display.text('$' + parseFloat(newValue || 0).toFixed(2));
                        } else {
                            $display.text(newValue || '-');
                        }

                        // Store as original value
                        $edit.data('original-value', newValue);

                        // Hide edit, show display
                        $edit.addClass('d-none');
                        $actions.addClass('d-none');
                        $display.removeClass('d-none');

                        // Show success message
                        if (typeof window.showAlert !== 'undefined') {
                            window.showAlert('success', response.data.message || 'Field updated successfully');
                        }

                        // Update header if it's a name field
                        if (field === 'name' && $('.header-card-title').length) {
                            $('.header-card-title').text(newValue);
                        }
                    }
                })
                .catch(function(error) {
                    const errorMessage = error.response?.data?.message ||
                                       error.response?.data?.error ||
                                       'Failed to update field';
                    if (typeof window.showAlert !== 'undefined') {
                        window.showAlert('danger', errorMessage);
                    }
                })
                .finally(function() {
                    $btnSave.prop('disabled', false).html('<i class="bi bi-check"></i> Save');
                });
            });

            // Save on Enter key (for input fields)
            $(document).on('keydown', '.field-edit', function(e) {
                if (e.key === 'Enter' && !e.shiftKey && $(this).is('input')) {
                    e.preventDefault();
                    $(this).closest('.editable-info-card').find('.btn-save-field').click();
                }
            });

            // Save on Enter key (for textarea - Ctrl+Enter)
            $(document).on('keydown', '.field-edit', function(e) {
                if (e.key === 'Enter' && e.ctrlKey && $(this).is('textarea')) {
                    e.preventDefault();
                    $(this).closest('.editable-info-card').find('.btn-save-field').click();
                }
            });

            // Store original values on page load
            $('.editable-info-card').each(function() {
                const $card = $(this);
                const $edit = $card.find('.field-edit');
                const $display = $card.find('.field-display');

                if ($edit.is('input, textarea')) {
                    $edit.data('original-value', $edit.val());
                } else if ($edit.is('select')) {
                    $edit.data('original-value', $edit.val());
                }
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initInlineEdit);
    } else {
        initInlineEdit();
    }
})();
