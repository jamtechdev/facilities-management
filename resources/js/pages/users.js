/**
 * Users Page JavaScript
 * Handles user management page interactions
 */

(function() {
    'use strict';

    /**
     * Initialize users page
     */
    function initUsersPage() {
        // Wait for jQuery to be available
        if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
            setTimeout(initUsersPage, 100);
            return;
        }

        const $ = window.jQuery;

        // View User
        $(document).on('click', '.view-user', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const $details = $('#userDetails');

            $details.html('<p class="text-muted">Loading...</p>');

            $.get(url)
                .done(function(user) {
                    const roles = user.roles.map(r => 
                        `<span class="badge bg-primary me-1">${r.name}</span>`
                    ).join('');

                    const html = `
                        <p><strong>Name:</strong> ${user.name}</p>
                        <p><strong>Email:</strong> ${user.email}</p>
                        <p><strong>Roles:</strong> ${roles || '<span class="text-muted">No roles</span>'}</p>
                        <p><strong>Created At:</strong> ${user.created_at}</p>
                        <p><strong>Updated At:</strong> ${user.updated_at || 'N/A'}</p>
                    `;

                    $details.html(html);
                })
                .fail(function(xhr) {
                    $details.html('<p class="text-danger">Error loading user details. Please try again.</p>');
                    console.error('Error loading user:', xhr);
                });
        });

        // Delete User
        $(document).on('click', '.delete-user', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this user?')) {
                return;
            }

            const url = $(this).attr('href');
            const $row = $(this).closest('tr');

            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        if (typeof window.showToast !== 'undefined') {
                            window.showToast('success', 'User deleted successfully');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success('User deleted successfully');
                        }
                    });
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Error deleting user';
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('error', message);
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    } else {
                        alert(message);
                    }
                }
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUsersPage);
    } else {
        initUsersPage();
    }
})();

