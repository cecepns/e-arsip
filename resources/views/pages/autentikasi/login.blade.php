@extends('layouts.base')

@section('title', 'Login | Sistem Dokumen')

@push('head')
<link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endpush

@section('content')
<!-- Dark Mode Toggle for Login -->
<div class="login-dark-mode-toggle">
    <button class="control-btn" id="loginDarkModeToggle" title="Toggle Dark Mode">
        <i class="fas fa-moon"></i>
    </button>
</div>

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
                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group mb-3">
                <label for="password" class="form-label">
                    <i class="bi bi-lock me-2"></i>Password
                </label>
                <div class="password-input">
                    <div class="w-100">
                        <input 
                            type="password" 
                            class="form-control simple-input @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="Masukkan password"
                        />
                        <div class="invalid-feedback"></div>
                    </div>
                    <button type="button" class="password-toggle" tabindex="-1" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary p-2 w-100 mt-2" id="loginSubmitBtn">
                Masuk
            </button>
        </form>
        <div class="login-footer">
            <span class="text-muted">&copy; {{ date('Y') }} E-Arsip Kantor Pajak</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
<script>
// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', function() {
    setupLoginDarkMode();
});

/**
 * ANCHOR: Setup Login Dark Mode
 * Setup dark mode functionality for login page
 */
function setupLoginDarkMode() {
    const darkModeToggle = document.getElementById('loginDarkModeToggle');
    const body = document.body;

    if (!darkModeToggle) return;

    // Check if dark mode is enabled
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    if (isDarkMode) {
        body.classList.add('dark-mode');
        updateLoginDarkModeIcon(true);
    }

    // Add click event listener
    darkModeToggle.addEventListener('click', function() {
        body.classList.toggle('dark-mode');
        const isNowDarkMode = body.classList.contains('dark-mode');
        localStorage.setItem('darkMode', isNowDarkMode);
        updateLoginDarkModeIcon(isNowDarkMode);
    });
}

/**
 * ANCHOR: Update Login Dark Mode Icon
 * Update the dark mode toggle icon
 */
function updateLoginDarkModeIcon(isDarkMode) {
    const icon = document.querySelector('#loginDarkModeToggle i');
    if (!icon) return;
    icon.className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
}

/**
 * ANCHOR: Toggle Password Visibility
 * Toggle password field visibility
 */
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

/**
 * ANCHOR: Login Form Handlers
 * Handle the login form submission with AJAX
 */
const loginHandlers = () => {
    const loginForm = document.getElementById('loginForm');
    const loginSubmitBtn = document.getElementById('loginSubmitBtn');
    const csrfToken = (
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
        document.querySelector('input[name="_token"]')?.value
    );

    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors(loginForm);
        setLoadingState(true, loginSubmitBtn);

        try {
            const formData = new FormData(loginForm);
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000);
            
            const response = await fetch(loginForm.action, {
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
                window.location.href = data.redirect_url || '/';
                return;
            }

            handleErrorResponse(data, loginForm);
            setLoadingState(false, loginSubmitBtn);
        } catch (error) {
            handleErrorResponse(error, loginForm);
            setLoadingState(false, loginSubmitBtn);
        }
    });
}

// ANCHOR: Initialize login handlers
loginHandlers();
</script>
@endpush
