/**
 * Entity Details Page JavaScript
 * Handles lead and client show pages functionality
 */

// Convert to Client functionality
function initConvertToClient() {
    const convertBtn = document.getElementById('convertToClientBtn');
    
    if (convertBtn) {
        convertBtn.addEventListener('click', function() {
            const leadId = convertBtn.getAttribute('data-lead-id');
            const btnText = convertBtn.innerHTML;
            
            if (confirm('Are you sure you want to convert this lead to a client? All notes, documents, communications, and feedback will be migrated to the client.')) {
                convertBtn.disabled = true;
                convertBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Converting...';
                
                axios.post(`/admin/leads/${leadId}/convert`, {}, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(function(response) {
                    if (response.data.success) {
                        window.location.href = response.data.redirect;
                    }
                })
                .catch(function(error) {
                    convertBtn.disabled = false;
                    convertBtn.innerHTML = btnText;
                    alert(error.response?.data?.message || 'Failed to convert lead');
                });
            }
        });
    }
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initConvertToClient();
    
    // Make openImageModal globally available
    window.openImageModal = openImageModal;
});

