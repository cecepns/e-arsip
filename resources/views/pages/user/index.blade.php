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
