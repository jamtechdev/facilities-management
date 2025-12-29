/**
 * Payouts Page JavaScript
 * Handles payout calculation and display
 */

(function() {
    'use strict';

    // Prevent multiple initializations
    let isInitialized = false;

    /**
     * Save payout result to localStorage
     */
    function savePayoutResult(result) {
        try {
            localStorage.setItem('payoutResult', JSON.stringify(result));
            localStorage.setItem('payoutFormData', JSON.stringify({
                staff_id: document.getElementById('staff_id')?.value,
                start_date: document.getElementById('start_date')?.value,
                end_date: document.getElementById('end_date')?.value
            }));
        } catch (e) {
            console.warn('Failed to save payout result to localStorage:', e);
        }
    }

    /**
     * Load and restore payout result from localStorage
     */
    function restorePayoutResult() {
        try {
            const savedResult = localStorage.getItem('payoutResult');
            const savedFormData = localStorage.getItem('payoutFormData');

            if (savedResult && savedFormData) {
                const result = JSON.parse(savedResult);
                const formData = JSON.parse(savedFormData);

                // Restore form values
                const staffSelect = document.getElementById('staff_id');
                const startDateInput = document.getElementById('start_date');
                const endDateInput = document.getElementById('end_date');

                if (staffSelect && formData.staff_id) {
                    staffSelect.value = formData.staff_id;
                }
                if (startDateInput && formData.start_date) {
                    startDateInput.value = formData.start_date;
                }
                if (endDateInput && formData.end_date) {
                    endDateInput.value = formData.end_date;
                }

                // Restore result display
                const resultDiv = document.getElementById('payoutResult');
                const resultContent = document.getElementById('resultContent');
                const downloadLink = document.getElementById('downloadLink');

                if (resultContent && result.staff) {
                    resultContent.innerHTML = `
                        <div class="row g-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Staff:</strong> ${result.staff.name}</p>
                                <p class="mb-1"><strong>Period:</strong> ${result.start_date} to ${result.end_date}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Total Worked Hours:</strong> ${result.total_worked_hours} hours</p>
                                <p class="mb-1"><strong>Payable Hours:</strong> ${result.total_payable_hours} hours</p>
                                <p class="mb-1"><strong>Hourly Rate:</strong> $${result.hourly_rate}</p>
                                <h4 class="mt-3 text-success"><strong>Total Payout: $${result.payout}</strong></h4>
                            </div>
                        </div>
                    `;
                }

                if (resultDiv) {
                    resultDiv.style.display = 'block';
                }

                if (downloadLink && window.payoutRoutes?.download && result.staff) {
                    downloadLink.href = `${window.payoutRoutes.download}?staff_id=${result.staff.id}&start_date=${result.start_date}&end_date=${result.end_date}`;
                }
            }
        } catch (e) {
            console.warn('Failed to restore payout result from localStorage:', e);
        }
    }

    /**
     * Clear saved payout result
     */
    function clearPayoutResult() {
        try {
            localStorage.removeItem('payoutResult');
            localStorage.removeItem('payoutFormData');
        } catch (e) {
            console.warn('Failed to clear payout result from localStorage:', e);
        }
    }

    /**
     * Initialize payouts page
     */
    function initPayoutsPage() {
        if (isInitialized) return;

        const payoutForm = document.getElementById('payoutForm');
        if (!payoutForm) return;

        isInitialized = true;

        // Restore previous result on page load
        restorePayoutResult();

        // Clear saved result when form values change
        const staffSelect = document.getElementById('staff_id');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function clearResultOnChange() {
            clearPayoutResult();
            const resultDiv = document.getElementById('payoutResult');
            if (resultDiv) {
                resultDiv.style.display = 'none';
            }
        }

        if (staffSelect) {
            staffSelect.addEventListener('change', clearResultOnChange);
        }
        if (startDateInput) {
            startDateInput.addEventListener('change', clearResultOnChange);
        }
        if (endDateInput) {
            endDateInput.addEventListener('change', clearResultOnChange);
        }

        // Prevent duplicate submissions
        let isSubmitting = false;

        payoutForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            // Prevent duplicate submissions
            if (isSubmitting) {
                return false;
            }
            isSubmitting = true;
            const staffId = document.getElementById('staff_id')?.value;
            const startDate = document.getElementById('start_date')?.value;
            const endDate = document.getElementById('end_date')?.value;

            if (!staffId || !startDate || !endDate) {
                isSubmitting = false;
                const message = 'Please select staff, start date, and end date';
                if (typeof window.showToast !== 'undefined') {
                    window.showToast('error', message);
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                } else {
                    alert(message);
                }
                return false;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Calculating...';

            try {
                const calculateRoute = window.payoutRoutes?.calculate || '/superadmin/payouts/calculate';
                const response = await fetch(calculateRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        staff_id: staffId,
                        start_date: startDate,
                        end_date: endDate
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const result = data.data;
                    const resultDiv = document.getElementById('payoutResult');
                    const downloadLink = document.getElementById('downloadLink');

                    const resultContent = document.getElementById('resultContent');
                    if (resultContent) {
                        resultContent.innerHTML = `
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Staff:</strong> ${result.staff.name}</p>
                                    <p class="mb-1"><strong>Period:</strong> ${result.start_date} to ${result.end_date}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Total Worked Hours:</strong> ${result.total_worked_hours} hours</p>
                                    <p class="mb-1"><strong>Payable Hours:</strong> ${result.total_payable_hours} hours</p>
                                    <p class="mb-1"><strong>Hourly Rate:</strong> $${result.hourly_rate}</p>
                                    <h4 class="mt-3 text-success"><strong>Total Payout: $${result.payout}</strong></h4>
                                </div>
                            </div>
                        `;
                    }

                    if (resultDiv) {
                        resultDiv.style.display = 'block';
                    }

                    if (downloadLink && window.payoutRoutes?.download) {
                        downloadLink.href = `${window.payoutRoutes.download}?staff_id=${result.staff.id}&start_date=${result.start_date}&end_date=${result.end_date}`;
                    }

                    // Save result to localStorage for persistence
                    savePayoutResult(result);
                } else {
                    throw new Error(data.message || 'Failed to calculate payout');
                }
            } catch (error) {
                const message = 'Failed to calculate payout: ' + (error.message || 'Unknown error');
                if (typeof window.showToast !== 'undefined') {
                    window.showToast('error', message);
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                } else {
                    alert(message);
                }
            } finally {
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
            return false;
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPayoutsPage);
    } else {
        initPayoutsPage();
    }
})();
