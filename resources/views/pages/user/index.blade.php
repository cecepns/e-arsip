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
@endsection

@push('scripts')
<script>
    const usersDataCurrentPage = {!! json_encode($users->items()) !!};
    
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
</script>
@endpush
