@extends('layouts.admin')

@section('title', 'Pengaturan | E-Arsip')

@section('admin-content')
<div class="container-fluid">
    <!-- ANCHOR: Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Pengaturan Instansi</h1>
                <p class="page-subtitle">Kelola informasi dan logo instansi</p>
            </div>
        </div>
    </div>

    <!-- ANCHOR: Settings Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Informasi Instansi
                    </h5>
                </div>
                <div class="card-body">
                    <form id="settingsForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- ANCHOR: Logo Section -->
                            <div class="col-md-4">
                                <div class="logo-section">
                                    <label class="form-label fw-bold">Logo Instansi</label>
                                    <div class="logo-upload-area">
                                        <div class="logo-preview" id="logoPreview">
                                            @if($pengaturan->logo)
                                                <img src="{{ Storage::url($pengaturan->logo) }}" alt="Logo Instansi" class="current-logo">
                                            @else
                                                <div class="no-logo">
                                                    <i class="fas fa-image"></i>
                                                    <p>Belum ada logo</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="logo-upload-controls">
                                            <input type="file" id="logo" name="logo" accept="image/*" class="form-control d-none">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('logo').click()">
                                                <i class="fas fa-upload me-1"></i>
                                                Pilih Logo
                                            </button>
                                            @if($pengaturan->logo)
                                                <button type="button" class="btn btn-outline-danger btn-sm ms-2" id="removeLogo">
                                                    <i class="fas fa-trash me-1"></i>
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                        <small class="text-muted">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB</small>
                                    </div>
                                </div>
                            </div>

                            <!-- ANCHOR: Institution Info Section -->
                            <div class="col-md-8">
                                <div class="institution-info">
                                    <div class="mb-3">
                                        <label for="nama_instansi" class="form-label fw-bold">
                                            Nama Instansi <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nama_instansi" 
                                               name="nama_instansi" 
                                               value="{{ old('nama_instansi', $pengaturan->nama_instansi) }}"
                                               placeholder="Masukkan nama instansi"
                                               required>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="alamat" class="form-label fw-bold">
                                            Alamat Instansi <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" 
                                                  id="alamat" 
                                                  name="alamat" 
                                                  rows="4"
                                                  placeholder="Masukkan alamat lengkap instansi"
                                                  required>{{ old('alamat', $pengaturan->alamat) }}</textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ANCHOR: Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-secondary" id="resetForm">
                                        <i class="fas fa-undo me-1"></i>
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="saveSettings">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan Pengaturan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.logo-section {
    text-align: center;
}

.logo-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.logo-upload-area:hover {
    border-color: #007bff;
    background-color: #e3f2fd;
}

.logo-preview {
    margin-bottom: 15px;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.current-logo {
    max-width: 100%;
    max-height: 120px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.no-logo {
    color: #6c757d;
    text-align: center;
}

.no-logo i {
    font-size: 2rem;
    margin-bottom: 10px;
    display: block;
}

.logo-upload-controls {
    margin-bottom: 10px;
}

.institution-info .form-label {
    color: #495057;
}

.institution-info .form-control {
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.institution-info .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
}

.page-subtitle {
    color: #6c757d;
    margin-bottom: 0;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 8px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 8px 8px 0 0 !important;
}

.card-title {
    color: #495057;
    font-weight: 600;
}

.btn {
    border-radius: 6px;
    font-weight: 500;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-outline-primary {
    color: #007bff;
    border-color: #007bff;
}

.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

.text-danger {
    color: #dc3545 !important;
}

.text-muted {
    color: #6c757d !important;
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}

.was-validated .form-control:invalid ~ .invalid-feedback,
.form-control.is-invalid ~ .invalid-feedback {
    display: block;
}

.was-validated .form-control:invalid,
.form-control.is-invalid {
    border-color: #dc3545;
}

.was-validated .form-control:invalid:focus,
.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const settingsForm = document.getElementById('settingsForm');
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logoPreview');
    const removeLogoBtn = document.getElementById('removeLogo');
    const resetBtn = document.getElementById('resetForm');
    const saveBtn = document.getElementById('saveSettings');

    // ANCHOR: Logo Preview Handler
    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.innerHTML = `<img src="${e.target.result}" alt="Logo Preview" class="current-logo">`;
                
                // Show remove button
                if (!removeLogoBtn) {
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-outline-danger btn-sm ms-2';
                    removeBtn.id = 'removeLogo';
                    removeBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Hapus';
                    removeBtn.addEventListener('click', removeLogo);
                    
                    const controls = document.querySelector('.logo-upload-controls');
                    controls.appendChild(removeBtn);
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // ANCHOR: Remove Logo Handler
    function removeLogo() {
        logoInput.value = '';
        logoPreview.innerHTML = `
            <div class="no-logo">
                <i class="fas fa-image"></i>
                <p>Belum ada logo</p>
            </div>
        `;
        
        const removeBtn = document.getElementById('removeLogo');
        if (removeBtn) {
            removeBtn.remove();
        }
    }

    if (removeLogoBtn) {
        removeLogoBtn.addEventListener('click', removeLogo);
    }

    // ANCHOR: Reset Form Handler
    resetBtn.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin mereset form? Semua perubahan yang belum disimpan akan hilang.')) {
            settingsForm.reset();
            removeLogo();
            
            // Reset to original values
            document.getElementById('nama_instansi').value = '{{ $pengaturan->nama_instansi }}';
            document.getElementById('alamat').value = '{{ $pengaturan->alamat }}';
            
            // Clear validation states
            settingsForm.classList.remove('was-validated');
            document.querySelectorAll('.form-control').forEach(input => {
                input.classList.remove('is-invalid');
            });
        }
    });

    // ANCHOR: Form Submission Handler
    settingsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous validation states
        settingsForm.classList.remove('was-validated');
        document.querySelectorAll('.form-control').forEach(input => {
            input.classList.remove('is-invalid');
        });

        // Show loading state
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';

        // Prepare form data
        const formData = new FormData(settingsForm);

        // Submit form
        fetch('{{ route("settings.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                Toastify({
                    text: data.message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745",
                    stopOnFocus: true
                }).showToast();

                // Update logo preview if new logo was uploaded
                if (data.logo_url) {
                    logoPreview.innerHTML = `<img src="${data.logo_url}" alt="Logo Instansi" class="current-logo">`;
                }
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById(field);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = data.errors[field][0];
                            }
                        }
                    });
                } else {
                    // Show error message
                    Toastify({
                        text: data.message || 'Terjadi kesalahan saat menyimpan pengaturan.',
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                        stopOnFocus: true
                    }).showToast();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Toastify({
                text: 'Terjadi kesalahan saat menyimpan pengaturan.',
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
                stopOnFocus: true
            }).showToast();
        })
        .finally(() => {
            // Reset button state
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Pengaturan';
        });
    });
});
</script>
@endpush
