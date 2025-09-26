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
    const usersDataCurrentPage = {!! json_encode($users) !!};

    /**
     * ANCHOR: Clear Errors
     * Clear the errors from the parent element
     * @param {Element} parentElement - The parent element to clear the errors from
     */
    const clearErrors = (parentElement) => {
        const invalidFields = parentElement.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => {
            field.classList.remove('is-invalid');
            const feedback = field.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.textContent = '';
        });
    }

    /**
     * ANCHOR: Add User Handlers
     * Handle the add user form submission
     */
    const addUserHandlers = () => {
        const addUserForm = document.getElementById('addUserForm');
        const addUserSubmitBtn = document.getElementById('addUserSubmitBtn');
        const addUserCancelBtn = document.getElementById('addUserCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        if (addUserForm) {
            addUserForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors(addUserForm);
                console.log('addUserSubmitBtn', addUserSubmitBtn);
                setLoadingState(true, addUserSubmitBtn);

                try {
                    const formData = new FormData(addUserForm);
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000);
                    const response = await fetchWithRetry(addUserForm.action, {
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
                        addUserForm.reset();
                        bootstrap.Modal.getInstance(document.getElementById('modalAddUser')).hide();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        handleErrorResponse(data, addUserForm);
                    }
                } catch (error) {
                    handleErrorResponse(error, addUserForm);
                } finally {
                    setLoadingState(false, addUserSubmitBtn);
                }
            });
        }
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
        const emailInput = document.getElementById('edit_email');
        const passwordInput = document.getElementById('edit_password');
        const roleInput = document.getElementById('edit_role');
        const bagianInput = document.getElementById('edit_bagian_id');

        const user = usersDataCurrentPage.data.find(user => user.id === userId);
        const { id, username, email, password, role, bagian } = user;

        idInput.value = id;
        usernameInput.value = username;
        emailInput.value = email;
        passwordInput.value = password;
        roleInput.value = role;
        bagianInput.value = bagian;

        editUserForm.action = `/user/${id}`;
    }

    /**
     * ANCHOR: Toggle Password
     * Toggle the password visibility
     * @param {number} userId - The id of the user to toggle the password for
     */
    const togglePassword = (userId) => {
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

    // ANCHOR: Run all handlers
    addUserHandlers();
</script>
@endpush
