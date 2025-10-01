@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    @include('partials.page-title', [
        'title' => 'Profile Saya',
        'subtitle' => 'Kelola informasi pribadi dan keamanan akun Anda.'
    ])
</div>

<div class="row">    
    <!-- Profile Information Card -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header py-3">
                <h5 class="card-title mb-0">
                    Informasi Profile
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="profile-photo-container mb-3 mt-3">
                    <img 
                        id="profilePhotoPreview" 
                        src="{{ $user->avatar_url }}" 
                        alt="{{ $user->nama }}" 
                        class="rounded-circle profile-photo"
                        style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #dee2e6;"
                    >
                </div>
                
                <div class="mb-3">
                    <button type="button" id="btnUploadFoto" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('foto').click()">
                        <i class="fas fa-upload me-1"></i> Upload Foto
                    </button>
                    <button type="button" id="btnHapusFoto" class="btn btn-outline-danger btn-sm ms-2" onclick="removePhoto()">
                        <i class="fas fa-trash me-1"></i> Hapus Foto
                    </button>
                </div>
                
                <div class="form-text">
                    Format: JPEG, PNG, JPG, GIF. Maksimal 2MB
                </div>
            </div>
            <div class="card-body">
                <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" value="{{ $user->username }}" readonly>
                                <div class="form-text">Username tidak dapat diubah</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" class="form-control" id="role" value="{{ $user->role }}" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" id="nama" value="{{ $user->nama }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" id="email" value="{{ $user->email }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ $user->phone }}" placeholder="081234567890">
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    @if($user->bagian)
                    <div class="mb-3">
                        <label for="bagian" class="form-label">Bagian</label>
                        <input type="text" class="form-control" id="bagian" value="{{ $user->bagian->nama_bagian }}" readonly>
                    </div>
                    @endif
                    
                    @if($user->isKepalaBagian())
                    <div class="mb-3">
                        <label for="kepala_bagian" class="form-label">Status</label>
                        <input type="text" class="form-control" id="kepala_bagian" value="Kepala Bagian" readonly>
                    </div>
                    @endif
                    
                    <!-- Hidden fields for photo -->
                    <input type="file" name="foto" id="foto" style="display: none;" accept="image/jpeg,image/png,image/jpg,image/gif">
                    <input type="hidden" id="hapus_foto" name="hapus_foto" value="0">
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id="profileSubmitBtn">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Password Change Card -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header py-3">
                <h5 class="card-title mb-0">
                    Ubah Password
                </h5>
            </div>
            <div class="card-body">
                <form id="passwordForm" action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password Lama <span class="text-danger">*</span>
                        </label>
                        <div class="password-input">
                            <div class="w-100">
                                <input 
                                    type="password" 
                                    name="current_password" 
                                    class="form-control simple-input" 
                                    id="current_password" 
                                    required
                                    placeholder="Masukkan password lama"
                                />
                                <div class="invalid-feedback"></div>
                            </div>
                            <button type="button" class="password-toggle" tabindex="-1" onclick="togglePasswordVisibility('current_password')">
                                <i class="fas fa-eye" id="current_password_icon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-key me-2"></i>Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="password-input">
                            <div class="w-100">
                                <input 
                                    type="password" 
                                    name="password" 
                                    class="form-control simple-input" 
                                    id="password" 
                                    required
                                    placeholder="Masukkan password baru"
                                />
                                <div class="invalid-feedback"></div>
                            </div>
                            <button type="button" class="password-toggle" tabindex="-1" onclick="togglePasswordVisibility('password')">
                                <i class="fas fa-eye" id="password_icon"></i>
                            </button>
                        </div>
                        <div class="form-text">Minimal 8 karakter dengan huruf besar, huruf kecil, angka, dan simbol</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-check-circle me-2"></i>Konfirmasi Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="password-input">
                            <div class="w-100">
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    class="form-control simple-input" 
                                    id="password_confirmation" 
                                    required
                                    placeholder="Konfirmasi password baru"
                                />
                                <div class="invalid-feedback"></div>
                            </div>
                            <button type="button" class="password-toggle" tabindex="-1" onclick="togglePasswordVisibility('password_confirmation')">
                                <i class="fas fa-eye" id="password_confirmation_icon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning" id="passwordSubmitBtn">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

    function updatePhotoButtons() {
        const hasFotoDB = {{ $user->foto ? 'true' : 'false' }};
        const fotoInput = document.getElementById('foto');
        const hasFotoInput = fotoInput && fotoInput.files.length > 0;
        const hapusFoto = document.getElementById('hapus_foto').value === '1';
        const btnUpload = document.getElementById('btnUploadFoto');
        const btnHapus = document.getElementById('btnHapusFoto');
        // Kondisi profile kosong: tidak ada di DB dan input kosong
        if ((!hasFotoDB && !hasFotoInput) || hapusFoto) {
            btnUpload.style.display = '';
            btnHapus.style.display = 'none';
        } else {
            btnUpload.style.display = 'none';
            btnHapus.style.display = '';
        }
    }

    /**
     * ANCHOR: Preview Profile Photo
     * Preview the selected photo before upload (manual save)
     */
    const previewProfilePhoto = (input) => {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePhotoPreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
            document.getElementById('hapus_foto').value = '0';
            updatePhotoButtons();
        }
    }

    /**
     * ANCHOR: Remove Photo
     * Remove the current profile photo (mark for deletion, not immediate)
     */
    const removePhoto = () => {
        document.getElementById('hapus_foto').value = '1';
        const initial = '{{ strtoupper(substr($user->nama, 0, 1)) }}';
        document.getElementById('profilePhotoPreview').src = `https://placehold.co/150x150?text=${initial}`;
        document.getElementById('foto').value = '';
        updatePhotoButtons();
    }

    /**
     * ANCHOR: Profile Form Handler
     * Handle the profile form submission
     */
    const profileFormHandler = () => {
        const profileForm = document.getElementById('profileForm');
        const profileSubmitBtn = document.getElementById('profileSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        profileForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(profileForm);
            setLoadingState(true, profileSubmitBtn);

            try {
                const formData = new FormData(profileForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetch(profileForm.action, {
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
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, profileForm);
                }
            } catch (error) {
                handleErrorResponse(error, profileForm);
            } finally {
                setLoadingState(false, profileSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Password Form Handler
     * Handle the password form submission
     */
    const passwordFormHandler = () => {
        const passwordForm = document.getElementById('passwordForm');
        const passwordSubmitBtn = document.getElementById('passwordSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        passwordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(passwordForm);
            setLoadingState(true, passwordSubmitBtn);

            try {
                const formData = new FormData(passwordForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(passwordForm.action, {
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
                    passwordForm.reset();
                } else {
                    handleErrorResponse(data, passwordForm);
                }
            } catch (error) {
                handleErrorResponse(error, passwordForm);
            } finally {
                setLoadingState(false, passwordSubmitBtn);
            }
        });
    }

    // ANCHOR: Initialize handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        profileFormHandler();
        passwordFormHandler();
        
        // Initialize photo upload handler
        const fotoInput = document.getElementById('foto');
        if (fotoInput) {
            fotoInput.addEventListener('change', function() {
                previewProfilePhoto(this);
            });
        }
        updatePhotoButtons(); // Initial call to set button visibility
    });
</script>
@endpush
