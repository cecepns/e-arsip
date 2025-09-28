@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    @include('partials.page-title', [
        'title' => 'Manajemen User',
        'subtitle' => 'Kelola akun dan hak akses pengguna sistem.'
    ])
</div>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUser">
        Tambah User
    </button>
    <form class="d-flex" style="max-width:300px;" method="GET" action="{{ route('user.index') }}">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari username, nama, atau email..." value="{{ $query ?? '' }}">
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
    - Ditemukan {{ $users->total() }} user
</div>
@endif

@include('partials.table', [
    'tableId' => 'userTable',
    'thead' => view()->make('pages.user._table._head')->render(),
    'tbody' => view()->make('pages.user._table._body', compact('users'))->render(),
])

@include('partials.pagination', [
    'currentPage' => $users->currentPage(),
    'totalPages' => $users->lastPage(),
    'baseUrl' => route('user.index'),
    'showInfo' => "Menampilkan {$users->firstItem()}-{$users->lastItem()} dari {$users->total()} user"
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
])

@include('partials.modal', [
    'type' => 'warning',
    'id' => 'modalResetPassword',
    'title' => 'Reset Password User',
    'size' => 'modal-md',
    'body' => view()->make('pages.user._reset_modal._body')->render(),
    'footer' => view()->make('pages.user._reset_modal._footer')->render(),
])
@endsection

@push('scripts')
<script>
    const usersDataCurrentPage = {!! json_encode($users->items()) !!};
    
    /**
     * ANCHOR: Edit User Handlers
     * Handle the edit user form submission
     */
    const editUserHandlers = () => {
        const editUserForm = document.getElementById('editUserForm');
        const editUserSubmitBtn = document.getElementById('editUserSubmitBtn');
        const editUserCancelBtn = document.getElementById('editUserCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        editUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(editUserForm);
            setLoadingState(true, editUserSubmitBtn);

            try {
                const formData = new FormData(editUserForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(editUserForm.action, {
                    method: 'POST',
                    body: formData,
                    signal: controller.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                clearTimeout(timeoutId);
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                }
                const data = await response.json();
                if (response.ok && data.success) {
                    showToast(data.message, 'success', 5000);
                    editUserForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalEditUser')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, editUserForm);
                }
            } catch (error) {
                handleErrorResponse(error, editUserForm);
            } finally {
                setLoadingState(false, editUserSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Reset Password Handlers
     * Handle the reset password form submission
     */
    const resetPasswordHandlers = () => {
        const resetPasswordForm = document.getElementById('resetPasswordForm');
        const resetPasswordSubmitBtn = document.getElementById('resetPasswordSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        resetPasswordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(resetPasswordForm);
            setLoadingState(true, resetPasswordSubmitBtn);

            try {
                const formData = new FormData(resetPasswordForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(resetPasswordForm.action, {
                    method: 'POST',
                    body: formData,
                    signal: controller.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                clearTimeout(timeoutId);
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                }
                const data = await response.json();
                if (response.ok && data.success) {
                    // Show password result in modal
                    showPasswordResult(data.new_password);
                    
                    // Hide submit button and show close button
                    document.getElementById('resetPasswordSubmitBtn').style.display = 'none';
                    document.getElementById('resetPasswordCancelBtn').style.display = 'none';
                    document.getElementById('resetPasswordCloseBtn').style.display = 'inline-block';
                    
                    // Don't close modal automatically
                    // Don't reload page automatically
                } else {
                    handleErrorResponse(data, resetPasswordForm);
                }
            } catch (error) {
                handleErrorResponse(error, resetPasswordForm);
            } finally {
                setLoadingState(false, resetPasswordSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Show Edit User Modal
     * Show the edit user modal
     * @param {number} id - The id of the user to edit
     */
    const showEditUserModal = (userId) => {
        const editUserForm = document.getElementById('editUserForm');
        const idInput = document.getElementById('edit_user_id');
        const usernameInput = document.getElementById('edit_username');
        const namaInput = document.getElementById('edit_nama');
        const emailInput = document.getElementById('edit_email');
        const phoneInput = document.getElementById('edit_phone');
        const passwordInput = document.getElementById('edit_password');
        const roleInput = document.getElementById('edit_role');
        const bagianInput = document.getElementById('edit_bagian_id');
        const isKepalaBagianInput = document.getElementById('edit_is_kepala_bagian');
        const user = usersDataCurrentPage.find(user => user.id === userId);
        const { id, username, nama, email, phone, password, role, bagian_id } = user;
        console.log('user', user);

        idInput.value = id;
        usernameInput.value = username || '';
        namaInput.value = nama || '';
        emailInput.value = email || '';
        phoneInput.value = phone || '';
        passwordInput.value = '';
        roleInput.value = role || '';
        bagianInput.value = bagian_id || '';
        
        // Check if user is kepala bagian
        isKepalaBagianInput.checked = user.bagian && user.bagian.kepala_bagian_user_id == id;

        editUserForm.action = `/user/${id}`;
    }

    /**
     * ANCHOR: Show Reset Password Modal
     * Show the reset password modal
     * @param {number} userId - The id of the user to reset password
     */
    const showResetPasswordModal = (userId) => {
        const resetUserName = document.getElementById('resetUserName');
        const resetPasswordForm = document.getElementById('resetPasswordForm');

        const user = usersDataCurrentPage.find(user => user.id === userId);
        const { id, username } = user;

        resetUserName.textContent = username;
        resetPasswordForm.action = `/user/${id}/reset-password`;
        
        // Reset modal state
        resetModalState();
    }

    /**
     * ANCHOR: Reset Modal State
     * Reset the modal to initial state
     */
    const resetModalState = () => {
        // Hide password result
        document.getElementById('passwordResult').style.display = 'none';
        
        // Show submit and cancel buttons
        document.getElementById('resetPasswordSubmitBtn').style.display = 'inline-block';
        document.getElementById('resetPasswordCancelBtn').style.display = 'inline-block';
        document.getElementById('resetPasswordCloseBtn').style.display = 'none';
    }

    /**
     * ANCHOR: Show Password Result
     * Show the generated password in the modal
     * @param {string} newPassword - The new generated password
     */
    const showPasswordResult = (newPassword) => {
        document.getElementById('newPasswordDisplay').value = newPassword;
        document.getElementById('passwordResult').style.display = 'block';
    }

    /**
     * ANCHOR: Copy Password
     * Copy the generated password to clipboard
     */
    const copyPassword = async () => {
        const passwordInput = document.getElementById('newPasswordDisplay');
        try {
            await navigator.clipboard.writeText(passwordInput.value);
            showToast('Password berhasil disalin ke clipboard!', 'success', 3000);
        } catch (err) {
            // Fallback for older browsers
            passwordInput.select();
            document.execCommand('copy');
            showToast('Password berhasil disalin ke clipboard!', 'success', 3000);
        }
    }

    /**
     * ANCHOR: Toggle Password Visibility
     * Toggle the password visibility for form inputs
     * @param {string} inputId - The id of the password input
     */
    const togglePasswordVisibility = (inputId) => {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(`${inputId}_icon`);
        
        if (passwordInput && toggleIcon) {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    }

    /**
     * ANCHOR: Handle checkbox kepala bagian for edit form
     * Enable/disable checkbox based on bagian selection and role
     */
    const handleEditKepalaBagianCheckbox = () => {
        const editBagianSelect = document.getElementById('edit_bagian_id');
        const editKepalaBagianCheckbox = document.getElementById('edit_is_kepala_bagian');
        const editRoleSelect = document.getElementById('edit_role');

        // Handle edit form
        if (editBagianSelect && editKepalaBagianCheckbox && editRoleSelect) {
            // Function to update bagian and kepala bagian based on role
            const updateEditBagianAndKepalaBagian = () => {
                if (editRoleSelect.value === 'Admin') {
                    // Admin role: disable bagian and kepala bagian
                    editBagianSelect.disabled = true;
                    editBagianSelect.value = '';
                    editKepalaBagianCheckbox.disabled = true;
                    editKepalaBagianCheckbox.checked = false;
                } else {
                    // Non-admin role: enable bagian selection
                    editBagianSelect.disabled = false;
                    editKepalaBagianCheckbox.disabled = editBagianSelect.value === '';
                }
            };

            // Listen for role changes
            editRoleSelect.addEventListener('change', updateEditBagianAndKepalaBagian);

            // Listen for bagian changes (only for non-admin roles)
            editBagianSelect.addEventListener('change', function() {
                if (editRoleSelect.value !== 'Admin') {
                    if (this.value) {
                        editKepalaBagianCheckbox.disabled = false;
                    } else {
                        editKepalaBagianCheckbox.disabled = true;
                        editKepalaBagianCheckbox.checked = false;
                    }
                }
            });

            // Initialize state
            updateEditBagianAndKepalaBagian();
        }
    }

    // ANCHOR: Run all handlers
    editUserHandlers();
    resetPasswordHandlers();
    handleEditKepalaBagianCheckbox();
    
    // ANCHOR: Reset modal state when modal is hidden
    document.getElementById('modalResetPassword').addEventListener('hidden.bs.modal', function () {
        resetModalState();
    });
</script>
@endpush
