@extends('layouts.base')

@section('title', 'Login | Sistem Dokumen')

@push('head')
<link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="login-container">
    <div class="login-box animate__animated animate__fadeInUp">
        <!-- Header -->
        <div class="login-header">
            <div class="logo-circle">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <h2 class="app-title">E-Arsip Kantor Pajak</h2>
            <p class="app-subtitle">Masuk ke akun Anda</p>
        </div>

        <!-- Alert Error -->
        @if(session('error'))
        <div class="alert alert-danger simple-alert animate__animated animate__shakeX" id="errorAlert" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
        </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login') }}" method="post" id="loginForm" class="login-form needs-validation" novalidate>
            @csrf
            <div class="form-group mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person me-2"></i>Username
                </label>
                <input 
                    type="text" 
                    class="form-control simple-input @error('username') is-invalid @enderror" 
                    id="username" 
                    name="username" 
                    value="{{ old('username') }}"
                    required 
                    autofocus 
                    placeholder="Masukkan username"
                />
                <div class="invalid-feedback">
                    @error('username')
                        {{ $message }}
                    @else
                        Username wajib diisi
                    @enderror
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="password" class="form-label">
                    <i class="bi bi-lock me-2"></i>Password
                </label>
                <div class="password-input">
                    <input 
                        type="password" 
                        class="form-control simple-input @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Masukkan password"
                    />
                    <button type="button" class="password-toggle" tabindex="-1" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
                <div class="invalid-feedback">
                    @error('password')
                        {{ $message }}
                    @else
                        Password wajib diisi
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-submit mt-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>
        <div class="login-footer">
            <span class="text-muted">&copy; {{ date('Y') }} E-Arsip Kantor Pajak</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}
</script>
@endpush
