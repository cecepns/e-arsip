@extends('layouts.admin')

@push('head')
<link href="{{ asset('css/user.css') }}" rel="stylesheet">
@endpush

@section('admin-content')
<div class="page-header">
    @include('partials.breadcrumb', [
        'items' => [
            ['label' => 'Home', 'url' => '#'],
            ['label' => 'Data User']
        ]
    ])
    @include('partials.page-title', [
        'title' => 'Manajemen User',
        'subtitle' => 'Kelola akun dan hak akses pengguna sistem.'
    ])
</div>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUser">
        <i class="fas fa-plus"></i> Tambah User
    </button>
    <form class="d-flex" style="max-width:300px;" method="GET" action="{{ route('user.index') }}">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari username atau email..." value="{{ $query ?? '' }}">
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
        @if(isset($query) && $query)
            <a href="{{ route('user.index') }}" class="btn btn-outline-danger ms-1" title="Clear search">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>
</div>

@if(isset($query) && $query)
<div class="alert alert-info mb-3">
    <i class="fas fa-search me-2"></i> 
    Hasil pencarian untuk: <strong>"{{ $query }}"</strong> 
    - Ditemukan {{ $users->count() }} user
</div>
@endif

@include('partials.table', [
    'tableId' => 'userTable',
    'thead' => view()->make('pages.user._table._head')->render(),
    'tbody' => view()->make('pages.user._table._body', compact('users'))->render(),
])

@include('partials.pagination', [
    'currentPage' => 1,
    'totalPages' => 2,
    'baseUrl' => '#',
    'showInfo' => 'Menampilkan 1-5 dari 8 user'
])

@include('partials.modal', [
    'id' => 'modalAddUser',
    'size' => 'modal-md',
    'title' => 'Tambah User',
    'body' => view('pages.user._form_modal._add_form', compact('bagian'))->render(),
])

@include('partials.modal', [
    'id' => 'modalEditUser',
    'size' => 'modal-md',
    'title' => 'Edit User',
    'body' => view('pages.user._form_modal._edit_form', compact('bagian'))->render(),
])

@include('partials.modal', [
    'type' => 'danger',
    'id' => 'modalDeleteUser',
    'title' => 'Konfirmasi Hapus User',
    'size' => 'modal-md',
    'body' => view()->make('pages.user._delete_modal._body')->render(),
    'footer' => view()->make('pages.user._delete_modal._footer')->render(),
])
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addUserForm = document.getElementById('addUserForm');
        const addUserSubmitBtn = document.getElementById('addUserSubmitBtn');
        const addUserCancelBtn = document.getElementById('addUserCancelBtn');

        if (addUserForm) {
            addUserForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Reset previous errors
                clearErrors();
                
                // Show loading state
                setLoadingState(true);
                
                try {
                    // Prepare form data
                    const formData = new FormData(addUserForm);
                    
                    // Create AbortController for timeout
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 seconds timeout
                    
                    // Send AJAX request with retry mechanism
                    const response = await fetchWithRetry(addUserForm.action, {
                        method: 'POST',
                        body: formData,
                        signal: controller.signal,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                        document.querySelector('input[name="_token"]')?.value
                        }
                    });
                    
                    clearTimeout(timeoutId);
                    
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }
                    
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        // Success - show success message and close modal
                        showSuccessMessage(data.message, data.timestamp);
                        addUserForm.reset();
                        bootstrap.Modal.getInstance(document.getElementById('modalAddUser')).hide();
                        
                        // Refresh the page to show new user
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Handle different types of errors
                        handleErrorResponse(data, response.status);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    handleNetworkError(error);
                } finally {
                    setLoadingState(false);
                }
            });
        }

        // ANCHOR: Enhanced Helper Functions
        function setLoadingState(loading) {
            const btnText = addUserSubmitBtn.querySelector('.btn-text');
            const spinner = addUserSubmitBtn.querySelector('.spinner-border');
            
            console.log(btnText);
            if (loading) {
                console.log('loading');
                addUserSubmitBtn.disabled = true;
                spinner.classList.remove('d-none');
                btnText.textContent = 'Menyimpan...';
            } else {
                addUserSubmitBtn.disabled = false;
                spinner.classList.add('d-none');
                btnText.textContent = 'Simpan';
            }
        }

        function clearErrors() {
            // Clear field errors
            const invalidFields = addUserForm.querySelectorAll('.is-invalid');
            invalidFields.forEach(field => {
                field.classList.remove('is-invalid');
                const feedback = field.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.textContent = '';
            });
        }

        // ANCHOR: Enhanced Error Handling Functions
        function handleErrorResponse(data, statusCode) {
            switch (data.error_type) {
                case 'validation':
                    showValidationErrors(data.errors);
                    showToast('Validasi gagal. Periksa form di bawah.', 'warning', 6000);
                    break;
                case 'database':
                    showToast(data.message, 'error', 7000);
                    if (data.debug) {
                        console.error('Database Error:', data.debug);
                    }
                    break;
                case 'general':
                    showToast(data.message, 'error', 7000);
                    if (data.debug) {
                        console.error('General Error:', data.debug);
                    }
                    break;
                default:
                    showToast(data.message || 'Terjadi kesalahan yang tidak diketahui.', 'error', 7000);
            }
        }

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
            
            // Show toast notification for network errors
            showToast(errorMessage, errorType, 7000);
        }

        function showValidationErrors(errors) {
            // Show field-specific errors
            Object.keys(errors).forEach(field => {
                const errorMessages = errors[field];
                
                // Show field-specific errors
                const fieldElement = addUserForm.querySelector(`[name="${field}"]`);
                if (fieldElement) {
                    fieldElement.classList.add('is-invalid');
                    const feedback = fieldElement.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.textContent = errorMessages[0];
                    }
                }
            });
        }


        // ANCHOR: Toastify Functions using app.css color scheme
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

        function showSuccessMessage(message, timestamp = null) {
            const timestampText = timestamp ? `<br><small style="opacity: 0.8;">${timestamp}</small>` : '';
            
            Toastify({
                text: `<i class="fas fa-check-circle me-2"></i>${message}${timestampText}`,
                duration: 5000,
                gravity: "top",
                position: "right",
                className: "toastify-success",
                escapeMarkup: false,
                onClick: function() {
                    this.hideToast();
                }
            }).showToast();
        }

        // ANCHOR: Retry Mechanism
        async function fetchWithRetry(url, options, maxRetries = 3) {
            let lastError;
            
            for (let attempt = 1; attempt <= maxRetries; attempt++) {
                try {
                    const response = await fetch(url, options);
                    
                    // If response is ok, return it
                    if (response.ok) {
                        return response;
                    }
                    
                    // If it's a client error (4xx), don't retry
                    if (response.status >= 400 && response.status < 500) {
                        return response;
                    }
                    
                    // For server errors (5xx), throw error to trigger retry
                    if (response.status >= 500) {
                        throw new Error(`Server error: ${response.status}`);
                    }
                    
                    return response;
                    
                } catch (error) {
                    lastError = error;
                    
                    // Don't retry on AbortError or client errors
                    if (error.name === 'AbortError' || 
                        (error.message && error.message.includes('Server error: 4'))) {
                        throw error;
                    }
                    
                    // Wait before retrying (exponential backoff)
                    if (attempt < maxRetries) {
                        const delay = Math.pow(2, attempt) * 1000; // 2s, 4s, 8s
                        console.warn(`Request failed (attempt ${attempt}/${maxRetries}), retrying in ${delay}ms...`);
                        await new Promise(resolve => setTimeout(resolve, delay));
                    }
                }
            }
            
            throw lastError;
        }
    });

    // ANCHOR: Existing Functions
    function editUser(button) {
        // Ambil data dari atribut data
        const id = button.getAttribute('data-id');
        const username = button.getAttribute('data-username');
        const email = button.getAttribute('data-email');
        const password = button.getAttribute('data-password');
        const role = button.getAttribute('data-role');
        const bagian = button.getAttribute('data-bagian') || ''; // Handle null/undefined

        console.log('Edit User Data:', { id, username, email, role, bagian }); // Debug log

        // Update form action
        const form = document.getElementById('editUserForm');
        form.action = `/user/${id}`;
        
        // Populate form dengan data yang ada
        document.getElementById('edit_user_id').value = id || '';
        document.getElementById('edit_username').value = username || '';
        document.getElementById('edit_email').value = email || '';
        document.getElementById('edit_password').value = ''; // Selalu kosongkan password saat edit
        document.getElementById('edit_role').value = role || 'Staf';
        document.getElementById('edit_bagian_id').value = bagian || '';
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalEditUser'));
        modal.show();
    }

    function deleteUser(button) {
        // Ambil data dari atribut data
        const id = button.getAttribute('data-id');
        const username = button.getAttribute('data-username');
        
        // Update nama user di modal
        document.getElementById('deleteUserName').textContent = username;
        
        // Update form action
        const form = document.getElementById('deleteUserForm');
        form.action = `/user/${id}`;
    }

    // Toggle password visibility function
    function togglePassword(userId) {
        const passwordElement = document.getElementById(`password-${userId}`);
        const toggleIcon = document.getElementById(`toggle-icon-${userId}`);
        
        if (passwordElement && toggleIcon) {
            // Get the original password from title attribute
            const originalPassword = passwordElement.getAttribute('title').replace('Password: ', '');
            
            // Check current state
            if (passwordElement.textContent === originalPassword) {
                // Hide password (show dots)
                passwordElement.textContent = '••••••••';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                toggleIcon.parentElement.title = 'Show password';
            } else {
                // Show password (show original)
                passwordElement.textContent = originalPassword;
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                toggleIcon.parentElement.title = 'Hide password';
            }
        }
    }
</script>
@endpush
