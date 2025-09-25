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
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUserForm">
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
    'thead' => view()->make('pages.user._table_head')->render(),
    'tbody' => view()->make('pages.user._table_body', compact('users'))->render(),
])

@include('partials.pagination', [
    'currentPage' => 1,
    'totalPages' => 2,
    'baseUrl' => '#',
    'showInfo' => 'Menampilkan 1-5 dari 8 user'
])

@include('partials.modal', [
    'id' => 'modalUserForm',
    'size' => 'modal-md',
    'title' => '<span id="modalTitle">Tambah User</span>',
    'body' => view('pages.user._form_modal', compact('bagian'))->render(),
])

@include('partials.modal', [
    'type' => 'danger',
    'id' => 'modalDeleteUser',
    'title' => 'Konfirmasi Hapus User',
    'size' => 'modal-md',
    'body' => view()->make('pages.user._delete_modal')->render(),
    'footer' => view()->make('pages.user._delete_modal_footer')->render(),
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

    // Update modal title
    document.getElementById('modalTitle').textContent = 'Edit User';
    
    // Update form action dan method
    const form = document.getElementById('userForm');
    form.action = `/user/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    
    // Update tombol submit
    document.getElementById('submitBtn').textContent = 'Update';
    
    // Update password field untuk edit mode
    const passwordInput = document.getElementById('password');
    passwordInput.required = false;
    passwordInput.placeholder = 'Kosongkan jika tidak ingin mengubah password';
    
    // Update password help text
    const passwordHelp = document.getElementById('passwordHelp');
    if (passwordHelp) {
        passwordHelp.textContent = 'Kosongkan jika tidak ingin mengubah password (password lama akan dipertahankan)';
    }
    
    // Populate form dengan data yang ada
    document.getElementById('user_id').value = id || '';
    document.getElementById('username').value = username || '';
    document.getElementById('email').value = email || '';
    document.getElementById('password').value = ''; // Selalu kosongkan password saat edit
    document.getElementById('role').value = role || 'Staf';
    document.getElementById('bagian_id').value = bagian || '';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('modalUserForm'));
    modal.show();
}

// Reset form ketika modal ditutup atau dibuka untuk tambah
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalUserForm');
    
    // Reset form ketika modal ditutup
    modal.addEventListener('hidden.bs.modal', function() {
        resetForm();
    });
    
    // Reset form ketika tombol tambah diklik (hanya jika bukan edit)
    document.querySelector('[data-bs-target="#modalUserForm"]').addEventListener('click', function() {
        // Delay kecil untuk memastikan modal sudah terbuka
        setTimeout(() => {
            if (document.getElementById('modalTitle').textContent === 'Tambah User') {
                resetForm();
            }
        }, 100);
    });
});

function resetForm() {
    // Reset modal title
    document.getElementById('modalTitle').textContent = 'Tambah User';
    
    // Reset form action dan method
    const form = document.getElementById('userForm');
    form.action = '{{ route("user.store") }}';
    document.getElementById('formMethod').value = 'POST';
    
    // Reset tombol submit
    document.getElementById('submitBtn').textContent = 'Simpan';
    
    // Reset password field untuk mode tambah
    const passwordInput = document.getElementById('password');
    passwordInput.required = true;
    passwordInput.placeholder = 'Password';
    
    // Reset password help text
    const passwordHelp = document.getElementById('passwordHelp');
    if (passwordHelp) {
        passwordHelp.textContent = 'Password akan disimpan dalam bentuk plain text';
    }
    
    // Clear form fields
    document.getElementById('user_id').value = '';
    document.getElementById('username').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('role').value = 'Staf';
    document.getElementById('bagian_id').value = '';
    
    // Reset form validation
    const formElements = form.querySelectorAll('.is-invalid');
    formElements.forEach(element => {
        element.classList.remove('is-invalid');
    });
    
    const errorMessages = form.querySelectorAll('.invalid-feedback');
    errorMessages.forEach(message => {
        message.remove();
    });
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
