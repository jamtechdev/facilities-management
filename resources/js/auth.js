// Auth Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Role selection handler for registration
    const roleClient = document.getElementById('role_client');
    const roleStaff = document.getElementById('role_staff');
    const clientFields = document.getElementById('clientFields');
    const companyNameInput = document.querySelector('input[name="company_name"]');
    let roleSelection = document.getElementById('roleSelection');
    let registerFormContainer = document.getElementById('registerFormContainer');
    const selectedRoleInput = document.getElementById('selectedRole');
    const selectedRoleText = document.getElementById('selectedRoleText');
    const backToRoleBtn = document.getElementById('backToRole');

    // Debug: Log if elements are found
    console.log('Register page elements:', {
        roleSelection: !!roleSelection,
        registerFormContainer: !!registerFormContainer,
        roleClient: !!roleClient,
        roleStaff: !!roleStaff
    });

    // Also try to find by class if ID doesn't work
    if (!roleSelection) {
        roleSelection = document.querySelector('.register-role-selection');
    }
    if (!registerFormContainer) {
        registerFormContainer = document.querySelector('.register-form-container');
    }

    // Ensure role selection is visible on page load and form is hidden
    if (roleSelection) {
        roleSelection.style.display = 'block';
        roleSelection.style.opacity = '1';
        roleSelection.style.visibility = 'visible';
        roleSelection.style.position = 'relative';
        roleSelection.style.zIndex = '10';
        roleSelection.style.width = '100%';
        roleSelection.style.height = 'auto';
        roleSelection.style.minHeight = '400px';
        roleSelection.style.color = '#333';
        roleSelection.style.background = 'transparent';
        roleSelection.classList.remove('hidden');
        roleSelection.style.pointerEvents = 'auto';
    }

    // Ensure form is hidden on page load
    if (registerFormContainer) {
        registerFormContainer.style.display = 'none';
        registerFormContainer.style.opacity = '0';
        registerFormContainer.style.visibility = 'hidden';
        registerFormContainer.classList.remove('show');
        registerFormContainer.style.pointerEvents = 'none';
    }

    // Force show register-right column
    const registerRight = document.querySelector('.register-right');
    if (registerRight) {
        registerRight.style.display = 'flex';
        registerRight.style.visibility = 'visible';
        registerRight.style.opacity = '1';
        registerRight.style.background = '#ffffff';
        registerRight.style.width = '100%';
        registerRight.style.minHeight = '100vh';
    }

    // Force show role selection container
    const roleSelectionContainer = document.querySelector('.role-selection-container');
    if (roleSelectionContainer) {
        roleSelectionContainer.style.display = 'block';
        roleSelectionContainer.style.visibility = 'visible';
        roleSelectionContainer.style.opacity = '1';
    }

    // Force show role cards
    const roleCards = document.querySelectorAll('.role-select-card');
    roleCards.forEach(card => {
        card.style.display = 'flex';
        card.style.visibility = 'visible';
        card.style.opacity = '1';
        card.style.background = '#ffffff';
        card.style.color = '#333';
    });

    // Mobile responsive adjustments
    function handleResize() {
        const isMobile = window.innerWidth <= 991;
        const registerRight = document.querySelector('.register-right');
        const roleSelection = document.getElementById('roleSelection');

        if (isMobile && registerRight) {
            registerRight.style.justifyContent = 'flex-start';
            registerRight.style.paddingTop = '1.5rem';
        } else if (registerRight) {
            registerRight.style.justifyContent = 'center';
            registerRight.style.paddingTop = '2rem';
        }

        if (isMobile && roleSelection) {
            roleSelection.style.minHeight = 'auto';
        }
    }

    // Handle resize
    window.addEventListener('resize', handleResize);
    handleResize(); // Initial call

    if (roleClient && roleStaff && roleSelection && registerFormContainer) {
        function showRegistrationForm(role) {
            console.log('Showing registration form for role:', role);

            if (!roleSelection || !registerFormContainer) {
                console.error('Role selection or form container not found!', {
                    roleSelection: !!roleSelection,
                    registerFormContainer: !!registerFormContainer
                });
                return;
            }

            // Hide role selection with fade out
            roleSelection.style.transition = 'opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease, height 0.3s ease';
            roleSelection.style.opacity = '0';
            roleSelection.style.transform = 'translateY(-20px)';
            roleSelection.style.visibility = 'hidden';
            roleSelection.classList.add('hidden');

            // Also hide the role selection container if it exists
            const roleSelectionContainer = document.querySelector('.role-selection-container');
            if (roleSelectionContainer) {
                roleSelectionContainer.style.transition = 'opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease';
                roleSelectionContainer.style.opacity = '0';
                roleSelectionContainer.style.visibility = 'hidden';
            }

            setTimeout(() => {
                // Completely hide role selection
                roleSelection.style.display = 'none';
                roleSelection.style.height = '0';
                roleSelection.style.overflow = 'hidden';
                roleSelection.style.margin = '0';
                roleSelection.style.padding = '0';
                roleSelection.style.minHeight = '0';
                roleSelection.style.maxHeight = '0';
                roleSelection.style.pointerEvents = 'none';

                // Hide role selection container
                if (roleSelectionContainer) {
                    roleSelectionContainer.style.display = 'none';
                    roleSelectionContainer.style.height = '0';
                    roleSelectionContainer.style.overflow = 'hidden';
                    roleSelectionContainer.style.pointerEvents = 'none';
                }

                // Set selected role
                if (selectedRoleInput) {
                    selectedRoleInput.value = role;
                }

                // Update role text
                if (selectedRoleText) {
                    if (role === 'client') {
                        selectedRoleText.textContent = 'Registering as Client - Fill in your details';
                    } else {
                        selectedRoleText.textContent = 'Registering as Staff - Fill in your details';
                    }
                }

                // Show form with fade in
                registerFormContainer.style.display = 'block';
                registerFormContainer.style.transition = 'opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease';
                registerFormContainer.classList.add('show');
                registerFormContainer.style.pointerEvents = 'auto';

                setTimeout(() => {
                    registerFormContainer.style.opacity = '1';
                    registerFormContainer.style.transform = 'translateY(0)';
                    registerFormContainer.style.visibility = 'visible';
                }, 50);
            }, 300);
        }

        function toggleClientFields() {
            if (roleClient.checked) {
                if (clientFields) {
                    clientFields.style.display = 'block';
                    setTimeout(() => {
                        clientFields.style.opacity = '1';
                        clientFields.style.transform = 'translateY(0)';
                    }, 50);
                }
                if (companyNameInput) {
                    companyNameInput.setAttribute('required', 'required');
                }
            } else {
                if (clientFields) {
                    clientFields.style.opacity = '0';
                    clientFields.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        clientFields.style.display = 'none';
                    }, 300);
                }
                if (companyNameInput) {
                    companyNameInput.removeAttribute('required');
                }
            }
        }

        function handleRoleSelection() {
            console.log('Role selection changed:', {
                client: roleClient.checked,
                staff: roleStaff.checked
            });

            if (roleClient.checked) {
                showRegistrationForm('client');
                setTimeout(() => toggleClientFields(), 350);
            } else if (roleStaff.checked) {
                showRegistrationForm('staff');
                if (clientFields) {
                    clientFields.style.display = 'none';
                }
                if (companyNameInput) {
                    companyNameInput.removeAttribute('required');
                }
            }
        }

        // Back to role selection
        if (backToRoleBtn) {
            backToRoleBtn.addEventListener('click', function() {
                // Hide form with fade out
                registerFormContainer.style.transition = 'opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease';
                registerFormContainer.style.opacity = '0';
                registerFormContainer.style.transform = 'translateY(20px)';
                registerFormContainer.style.visibility = 'hidden';
                registerFormContainer.classList.remove('show');

                setTimeout(() => {
                    registerFormContainer.style.display = 'none';

                    // Show role selection with fade in
                    roleSelection.style.display = 'block';
                    roleSelection.style.height = 'auto';
                    roleSelection.style.overflow = 'visible';
                    roleSelection.style.margin = '0';
                    roleSelection.style.padding = '0';
                    roleSelection.style.minHeight = '400px';
                    roleSelection.classList.remove('hidden');
                    roleSelection.style.transition = 'opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease';

                    setTimeout(() => {
                        roleSelection.style.opacity = '1';
                        roleSelection.style.transform = 'translateY(0)';
                        roleSelection.style.visibility = 'visible';
                    }, 50);

                    // Uncheck roles
                    if (roleClient) roleClient.checked = false;
                    if (roleStaff) roleStaff.checked = false;
                }, 300);
            });
        }

        // Add event listeners - use both change and click events
        roleClient.addEventListener('change', handleRoleSelection);
        roleStaff.addEventListener('change', handleRoleSelection);

        // Also add click listeners to the labels for immediate response
        const roleClientLabel = document.querySelector('label[for="role_client"]');
        const roleStaffLabel = document.querySelector('label[for="role_staff"]');

        if (roleClientLabel) {
            roleClientLabel.addEventListener('click', function(e) {
                // Small delay to ensure radio is checked
                setTimeout(() => {
                    if (roleClient.checked) {
                        console.log('Client label clicked');
                        handleRoleSelection();
                    }
                }, 10);
            });
        }

        if (roleStaffLabel) {
            roleStaffLabel.addEventListener('click', function(e) {
                // Small delay to ensure radio is checked
                setTimeout(() => {
                    if (roleStaff.checked) {
                        console.log('Staff label clicked');
                        handleRoleSelection();
                    }
                }, 10);
            });
        }

        // If role is already selected (from old input), show form
        if (roleClient.checked || roleStaff.checked) {
            if (roleClient.checked) {
                showRegistrationForm('client');
                setTimeout(() => toggleClientFields(), 350);
            } else {
                showRegistrationForm('staff');
            }
        }
    }
    // Password Toggle Functionality - Handle all password fields with toggle
    document.querySelectorAll('[id^="togglePassword_"]').forEach(function(toggleBtn) {
        const fieldName = toggleBtn.id.replace('togglePassword_', '');
        const passwordInput = document.getElementById(fieldName);
        const eyeIcon = document.getElementById('eyeIcon_' + fieldName);

        if (passwordInput && eyeIcon) {
            toggleBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'password') {
                    eyeIcon.classList.remove('bi-eye-slash');
                    eyeIcon.classList.add('bi-eye');
                } else {
                    eyeIcon.classList.remove('bi-eye');
                    eyeIcon.classList.add('bi-eye-slash');
                }
            });
        }
    });

    // Form Validation Enhancement and Loader
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                return;
            }

            // Show global loader on form submission (only if preloader is hidden)
            if (typeof window.showGlobalLoader === 'function') {
                // Check if preloader is still visible
                const preloader = document.getElementById('preloader');
                const isPreloaderVisible = preloader &&
                    !preloader.classList.contains('hide') &&
                    preloader.style.display !== 'none' &&
                    window.getComputedStyle(preloader).display !== 'none';

                if (!isPreloaderVisible) {
                    const formAction = form.getAttribute('action');
                    let message = 'Processing...';

                    if (formAction && formAction.includes('login')) {
                        message = 'Logging in...';
                    } else if (formAction && formAction.includes('register')) {
                        message = 'Creating account...';
                    }

                    window.showGlobalLoader(message);
                } else {
                    // Wait for preloader to hide, then show global loader
                    window.addEventListener('preloaderHidden', function() {
                        const formAction = form.getAttribute('action');
                        let message = 'Processing...';

                        if (formAction && formAction.includes('login')) {
                            message = 'Logging in...';
                        } else if (formAction && formAction.includes('register')) {
                            message = 'Creating account...';
                        }

                        window.showGlobalLoader(message);
                    }, { once: true });
                }
            }
        });
    });

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
