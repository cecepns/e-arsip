/**
 * ANCHOR: Show Validation Errors
 * Shows validation errors for a form.
 * @param {Object} errors - The errors to show.
 */
function showValidationErrors(errors, formElement) {
    Object.keys(errors).forEach(field => {
        const errorMessages = errors[field];
        
        const fieldElement = formElement.querySelector(`[name="${field}"]`);
        if (fieldElement) {
            fieldElement.classList.add('is-invalid');
            const feedback = fieldElement.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = errorMessages[0];
            }
        }
    });
}

/**
 * ANCHOR: Fetch with Retry
 * Performs a fetch request with retry and timeout support.
 * @param {string} url - The endpoint URL.
 * @param {Object} options - Fetch options (method, headers, body, etc).
 * @param {number} maxRetries - Maximum retry attempts.
 * @param {number} timeoutMs - Timeout in milliseconds.
 * @returns {Promise<Response>} - Fetch response.
 */
async function fetchWithRetry(url, options = {}, maxRetries = 2, timeoutMs = 30000) {
    let lastError;
    for (let attempt = 0; attempt <= maxRetries; attempt++) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), timeoutMs);
        try {
            const response = await fetch(url, { ...options, signal: controller.signal });
            clearTimeout(timeoutId);
            if (!response.ok && response.status >= 500) {
                throw new Error(`Server error: ${response.status}`);
            }
            return response;
        } catch (error) {
            clearTimeout(timeoutId);
            lastError = error;
            // Don't retry on AbortError or client errors
            if (error.name === 'AbortError' || (error.message && error.message.includes('Server error: 4'))) {
                throw error;
            }
            // Exponential backoff before retrying
            if (attempt < maxRetries) {
                const delay = Math.pow(2, attempt) * 1000;
                await new Promise(resolve => setTimeout(resolve, delay));
            }
        }
    }
    throw lastError;
}

/**
 * ANCHOR: Show Toast
 * Shows a toast notification.
 * @param {string} message - The message to show.
 * @param {string} type - The type of toast (success, error, warning, info).
 * @param {number} duration - The duration of the toast in milliseconds.
 */
function showToast(message, type = 'info', duration = 5000) {
    const configs = {
        success: {
            className: "toastify-success",
            icon: "fa-check-circle"
        },
        error: {
            className: "toastify-error", 
            icon: "fa-exclamation-circle"
        },
        warning: {
            className: "toastify-warning",
            icon: "fa-exclamation-triangle"
        },
        info: {
            className: "toastify-info",
            icon: "fa-info-circle"
        }
    };

    const config = configs[type] || configs.info;
    
    Toastify({
        text: `<i class="fas ${config.icon} me-2"></i>${message}`,
        duration: duration,
        gravity: "top",
        position: "right",
        className: config.className,
        escapeMarkup: false,
        onClick: function() {
            this.hideToast();
        }
    }).showToast();
}

/**
 * ANCHOR: Handle Error Response
 * Handles an error response from the server.
 * @param {Object} data - The error response data.
 */
function handleErrorResponse(data, formElement) {
    switch (data.error_type) {
        case 'validation':
            showValidationErrors(data.errors, formElement);
            showToast('Validasi gagal. Periksa form di bawah.', 'warning');
            break;
        case 'database':
            showToast(data.message, 'error');
            if (data.debug) {
                console.error('Database Error:', data.debug);
            }
            break;
        case 'general':
            showToast(data.message, 'error');
            if (data.debug) {
                console.error('General Error:', data.debug);
            }
            break;
        default:
            showToast(data.message || 'Terjadi kesalahan yang tidak diketahui.', 'error');
    }
}

/**
 * ANCHOR: Handle Network Error
 * Handles a network error.
 * @param {Error} error - The error to handle.
 */
function handleNetworkError(error) {
    let errorMessage = 'Terjadi kesalahan jaringan.';
    let errorType = 'warning';
    
    if (error.name === 'AbortError') {
        errorMessage = 'Request timeout. Silakan coba lagi.';
        errorType = 'warning';
    } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
        errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
        errorType = 'error';
    } else if (error.message === 'Response is not JSON') {
        errorMessage = 'Server mengembalikan response yang tidak valid.';
        errorType = 'error';
    } else if (error.message && error.message.includes('Server error: 5')) {
        errorMessage = 'Server sedang mengalami masalah. Silakan coba lagi.';
        errorType = 'warning';
    }    
    showToast(errorMessage, errorType);
}

/**
 * ANCHOR: Set Loading State
 * Sets the loading state of a button.
 * @param {boolean} loading - The loading state.
 * @param {Element} btnElement - The button element to set the loading state for.
 */
/**
 * ANCHOR: Set Loading State
 * Sets the loading state of a button, rendering spinner as an element.
 * @param {boolean} loading - The loading state.
 * @param {Element} btnElement - The button element to set the loading state for.
 */
function setLoadingState(loading, btnElement) {
    let spinner = btnElement.querySelector('.spinner-border');
    if (!spinner) {
        spinner = document.createElement('span');
        spinner.style.marginRight = '5px';
        spinner.className = 'spinner-border spinner-border-sm d-none';
        spinner.setAttribute('role', 'status');
        spinner.setAttribute('aria-hidden', 'true');
        btnElement.prepend(spinner);
    }

    if (loading) {
        btnElement.disabled = true;
        spinner.classList.remove('d-none');
    } else {
        btnElement.disabled = false;
        spinner.classList.add('d-none');
    }
}

/**
 * ANCHOR: Clear Errors
 * Clears the errors from the parent element
 * @param {Element} parentElement - The parent element to clear the errors from
 */
function clearErrors(parentElement) {
    const invalidFields = parentElement.querySelectorAll('.is-invalid');
    invalidFields.forEach(field => {
        field.classList.remove('is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) feedback.textContent = '';
    });
}
