@extends('layouts.admin')

@section('title', 'Pengaturan | E-Arsip')

@section('admin-content')
<!-- ANCHOR: Page Header -->
<div class="page-header">
    @include('partials.page-title', [
        'title' => 'Pengaturan Instansi',
        'subtitle' => 'Kelola informasi dan logo instansi'
    ])
</div>

<!-- ANCHOR: Settings Form -->
<div class="card">
    <div class="card-body">
        <form id="settingsForm" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="hapus_logo" name="hapus_logo" value="0">
            <div class="row">
                <!-- ANCHOR: Logo Section -->
                <div class="col-md-4">
                    <div class="logo-section">
                        <label class="form-label fw-bold mb-4">Logo Instansi</label>
                        <div class="logo-upload-area">
                            <div class="logo-preview mb-3 d-flex justify-content-center" id="logoPreview">
                                @if($pengaturan->logo)
                                    <img src="{{ Storage::url($pengaturan->logo) }}" style="max-width: 250px;" alt="Logo Instansi" class="current-logo w-100">
                                @else
                                    <div class="p-5 text-center border rounded-3" style="max-width: 250px;">
                                        Belum ada logo
                                    </div>
                                @endif
                            </div>
                            <div class="logo-upload-controls">
                                <input type="file" id="logo" name="logo" accept="image/*" class="form-control d-none">
                                <button type="button" class="btn btn-outline-primary btn w-100 mb-2" onclick="document.getElementById('logo').click()">
                                    <i class="fas fa-upload me-1"></i>
                                    Pilih Logo
                                </button>
                                @if($pengaturan->logo)
                                    <button type="button" class="btn btn-danger btn w-100 mb-2" id="removeLogo">
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

                        <div class="mb-3">
                            <label for="no_telp" class="form-label fw-bold">
                                Nomor Telepon
                            </label>
                            <input type="text" 
                                    class="form-control" 
                                    id="no_telp" 
                                    name="no_telp" 
                                    value="{{ old('no_telp', $pengaturan->no_telp) }}"
                                    placeholder="Masukkan nomor telepon instansi"
                                    maxlength="20">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">
                                Email Instansi
                            </label>
                            <input type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', $pengaturan->email) }}"
                                    placeholder="Masukkan alamat email instansi"
                                    maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANCHOR: Form Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id="saveSettings">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ANCHOR: Cache pengaturan data globally
    window.pengaturanData = {!! json_encode([
        'nama_instansi' => $pengaturan->nama_instansi,
        'alamat' => $pengaturan->alamat,
        'no_telp' => $pengaturan->no_telp,
        'email' => $pengaturan->email,
        'logo' => $pengaturan->logo
    ]) !!};

    
    /**
     * ANCHOR: Remove Logo Handler
     * Remove logo preview and reset input
     */
    const removeLogo = () => {
        const hapusLogoInput = document.getElementById('hapus_logo');
        const logoInput = document.getElementById('logo');
        const logoPreview = document.getElementById('logoPreview');
        
        hapusLogoInput.value = '1';
        logoInput.value = '';
        logoPreview.innerHTML = `
            <div class="p-5 text-center border rounded-3" style="max-width: 250px;">
                Belum ada logo
            </div>
        `;
        
        const removeBtn = document.getElementById('removeLogo');
        if (removeBtn) {
            removeBtn.remove();
        }
    }

    /**
     * ANCHOR: Logo Preview Handler
     * Handle logo file input change and show preview
     */
    const handleLogoPreview = () => {
        const hapusLogoInput = document.getElementById('hapus_logo');
        const logoInput = document.getElementById('logo');
        const logoPreview = document.getElementById('logoPreview');
        
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                showToast('Format file tidak valid. Gunakan JPEG, PNG, JPG, atau GIF.', 'warning');
                hapusLogoInput.value = '0';
                logoInput.value = '';
                return;
            }

            // Validate file size (2MB = 2 * 1024 * 1024 bytes)
            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                showToast('Ukuran file terlalu besar. Maksimal 2MB.', 'warning');
                hapusLogoInput.value = '0';
                logoInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.innerHTML = `<img src="${e.target.result}" style="max-width: 250px;" alt="Logo Preview" class="current-logo w-100">`;
                hapusLogoInput.value = '0';
                // Show remove button if not exists
                if (!document.getElementById('removeLogo')) {
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-danger btn w-100 mb-2';
                    removeBtn.id = 'removeLogo';
                    removeBtn.innerHTML = 'Hapus';
                    removeBtn.addEventListener('click', removeLogo);
                    
                    const controls = document.querySelector('.logo-upload-controls');
                    controls.appendChild(removeBtn);
                }
            };
            reader.readAsDataURL(file);
        });

        // Attach event to existing remove button
        const removeLogoBtn = document.getElementById('removeLogo');
        if (removeLogoBtn) {
            removeLogoBtn.addEventListener('click', removeLogo);
        }
    }

    /**
     * ANCHOR: Settings Form Submission Handler
     * Handle form submission with retry and timeout
     */
    const handleSettingsFormSubmit = () => {
        const settingsForm = document.getElementById('settingsForm');
        const saveBtn = document.getElementById('saveSettings');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        settingsForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Clear previous validation states
            clearErrors(settingsForm);
            
            // Show loading state
            setLoadingState(true, saveBtn);

            try {
                const formData = new FormData(settingsForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                
                const response = await fetch(settingsForm.action, {
                    method: 'POST',
                    body: formData,
                    signal: controller.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                clearTimeout(timeoutId);
                
                // Validate response content type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                }
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Show success message
                    showToast(data.message, 'success', 5000);

                    // Update logo preview if new logo was uploaded
                    if (data.logo_url) {
                        const logoPreview = document.getElementById('logoPreview');
                        logoPreview.innerHTML = `<img src="${data.logo_url}" style="max-width: 250px;" alt="Logo Instansi" class="current-logo w-100">`;
                        
                        // Update cached data
                        window.pengaturanData.logo = data.logo_url;
                    }

                    // Update cached data
                    window.pengaturanData.nama_instansi = formData.get('nama_instansi');
                    window.pengaturanData.alamat = formData.get('alamat');
                    window.pengaturanData.no_telp = formData.get('no_telp');
                    window.pengaturanData.email = formData.get('email');
                } else {
                    // Handle error response
                    handleErrorResponse(data, settingsForm);
                }
            } catch (error) {
                console.error('Error:', error);
                handleNetworkError(error);
            } finally {
                // Reset button state
                setLoadingState(false, saveBtn);
            }
        });
    }

    // ANCHOR: Initialize all handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        handleLogoPreview();
        handleSettingsFormSubmit();
    });
</script>
@endpush
