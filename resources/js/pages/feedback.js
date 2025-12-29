/**
 * Feedback Form JavaScript
 * Handles feedback form submission and rating stars
 */

(function() {
    'use strict';

    /**
     * Initialize feedback page
     */
    function initFeedbackPage() {
        // Rating stars
        document.querySelectorAll('.rating-star').forEach(function(star) {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                document.querySelectorAll('.rating-star').forEach(function(s, i) {
                    if (i < rating) {
                        s.innerHTML = '<i class="bi bi-star-fill text-warning"></i>';
                    } else {
                        s.innerHTML = '<i class="bi bi-star"></i>';
                    }
                });
                
                const ratingInput = document.getElementById('rating' + rating);
                if (ratingInput) {
                    ratingInput.checked = true;
                }
            });
        });

        // Form submission
        const feedbackForm = document.getElementById('feedbackForm');
        if (feedbackForm) {
            feedbackForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

                try {
                    const feedbackRoute = window.feedbackRoute || this.action;
                    const response = await fetch(feedbackRoute, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        if (typeof window.showToast !== 'undefined') {
                            window.showToast('success', data.message || 'Feedback submitted successfully');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success(data.message || 'Feedback submitted successfully');
                        } else {
                            alert(data.message || 'Feedback submitted successfully');
                        }
                        this.reset();
                    } else {
                        throw new Error(data.message || 'Failed to submit feedback');
                    }
                } catch (error) {
                    const message = 'Error: ' + (error.message || 'Unknown error');
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('error', message);
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    } else {
                        alert(message);
                    }
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFeedbackPage);
    } else {
        initFeedbackPage();
    }
})();

