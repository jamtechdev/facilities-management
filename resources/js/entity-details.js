/**
 * Entity Details Page JavaScript
 * Handles lead and client show pages functionality
 */

// Convert to Client functionality
let convertToClientInitialized = false;

function initConvertToClient() {
    if (convertToClientInitialized) return;

    const convertBtn = document.getElementById('convertToClientBtn');
    
    if (convertBtn && !convertBtn.dataset.handlerAttached) {
        convertBtn.dataset.handlerAttached = 'true';
        convertToClientInitialized = true;

        convertBtn.addEventListener('click', function() {
            const leadId = convertBtn.getAttribute('data-lead-id');
            const btnText = convertBtn.innerHTML;
            
            if (confirm('Are you sure this qualified lead wants to convert?')) {
                convertBtn.disabled = true;
                convertBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Converting...';
                
                // Show global loader
                if (typeof window.showGlobalLoader !== 'undefined') {
                    window.showGlobalLoader('Converting lead to client... Migrating all data (communications, documents, feedback)...');
                }
                
                // Get route from button data attribute or window object
                const convertUrl = convertBtn.getAttribute('data-convert-url') || window.convertLeadRoute;

                if (!convertUrl) {
                    console.error('Convert lead route not found');
                    convertBtn.disabled = false;
                    convertBtn.innerHTML = btnText;
                    if (typeof window.hideGlobalLoader !== 'undefined') {
                        window.hideGlobalLoader();
                    }
                    return;
                }

                axios.post(convertUrl, {}, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(function(response) {
                    if (response.data.success) {
                        // Update loader message
                        if (typeof window.showGlobalLoader !== 'undefined') {
                            window.showGlobalLoader('Conversion successful! Redirecting...');
                        }
                        // Redirect after a short delay
                        setTimeout(function() {
                            window.location.href = response.data.redirect;
                        }, 500);
                    }
                })
                .catch(function(error) {
                    // Hide global loader on error
                    if (typeof window.hideGlobalLoader !== 'undefined') {
                        window.hideGlobalLoader();
                    }
                    convertBtn.disabled = false;
                    convertBtn.innerHTML = btnText;
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('error', error.response?.data?.message || 'Failed to convert lead');
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(error.response?.data?.message || 'Failed to convert lead');
                    } else {
                        alert(error.response?.data?.message || 'Failed to convert lead');
                    }
                });
            }
        });
    }
}

// Tab persistence functionality
function initTabPersistence() {
    // Find all tab navigation elements
    const tabNavs = document.querySelectorAll('[role="tablist"]');

    tabNavs.forEach(function (tabNav) {
        const tabId = tabNav.id;
        const tabContent = document.getElementById(tabId + 'Content');

        if (!tabContent) return;

        // Get active tab from URL hash or localStorage
        const urlHash = window.location.hash.replace('#', '');
        const storageKey = 'active_tab_' + tabId;

        let activeTabId = null;

        // Priority: URL hash > localStorage > first tab
        if (urlHash) {
            const tabButton = tabNav.querySelector(`button[data-bs-target="#${urlHash}"]`);
            if (tabButton) {
                activeTabId = urlHash;
            }
        }

        if (!activeTabId) {
            activeTabId = localStorage.getItem(storageKey);
        }

        // Activate the tab
        if (activeTabId) {
            const tabButton = tabNav.querySelector(`button[data-bs-target="#${activeTabId}"]`);
            const tabPane = tabContent.querySelector(`#${activeTabId}`);

            if (tabButton && tabPane) {
                // Remove active class from all tabs
                tabNav.querySelectorAll('.nav-link').forEach(function (btn) {
                    btn.classList.remove('active');
                });
                tabContent.querySelectorAll('.tab-pane').forEach(function (pane) {
                    pane.classList.remove('show', 'active');
                });

                // Activate selected tab
                tabButton.classList.add('active');
                tabPane.classList.add('show', 'active');
            }
        }

        // Save tab state when clicked
        tabNav.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function (button) {
            button.addEventListener('shown.bs.tab', function (event) {
                const targetId = event.target.getAttribute('data-bs-target').replace('#', '');
                localStorage.setItem(storageKey, targetId);

                // Update URL hash without triggering scroll
                const currentUrl = window.location.href.split('#')[0];
                window.history.replaceState(null, '', currentUrl + '#' + targetId);
            });
        });
    });
}

// Image Modal functionality
function openImageModal(imageSrc, photoType) {
    const modalImage = document.getElementById('modalImage');
    const photoTypeLabel = document.getElementById('photoType');
    
    if (modalImage && photoTypeLabel) {
        modalImage.src = imageSrc;
        photoTypeLabel.textContent = photoType.charAt(0).toUpperCase() + photoType.slice(1);
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }
}

// Prevent duplicate initialization
let entityDetailsInitialized = false;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (entityDetailsInitialized) return;
    entityDetailsInitialized = true;

    initConvertToClient();
    initTabPersistence();
    
    // Make openImageModal globally available
    window.openImageModal = openImageModal;
});

