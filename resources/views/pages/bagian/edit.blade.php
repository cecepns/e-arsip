@extends('layouts.admin')

@push('head')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('admin-content')
<!-- Page Header -->
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Data Bagian</a></li>
            <li class="breadcrumb-item active">Edit Bagian</li>
        </ol>
    </nav>
    <div class="page-title">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Edit Bagian</h2>
                <p class="page-subtitle mb-0">Ubah informasi bagian/divisi</p>
            </div>
            <div>
                <a href="#" class="btn btn-secondary" onclick="goBack()">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Form Section -->
<div class="form-section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-edit me-2"></i>
                Form Edit Bagian
            </h5>
        </div>
        <div class="card-body">
            <form id="editForm" novalidate>
                <input type="hidden" id="bagian_id" name="id" value="1">
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="nama_bagian" class="form-label">
                            Nama Bagian <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nama_bagian" name="nama_bagian" 
                               placeholder="Masukkan nama bagian/divisi" required>
                        <div class="invalid-feedback">
                            Nama bagian harus diisi
                        </div>
                        <div class="form-text">
                            Contoh: Sumber Daya Manusia, Keuangan, Pengadaan, dll.
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="4" 
                                  placeholder="Deskripsi atau penjelasan tentang bagian ini (opsional)"></textarea>
                        <div class="form-text">
                            Berikan deskripsi singkat tentang fungsi dan tanggung jawab bagian ini.
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary me-2" onclick="goBack()">
                        <i class="fas fa-times me-2"></i>
                        Batal
                    </button>
                    <button type="button" class="btn btn-primary" onclick="updateData()">
                        <i class="fas fa-save me-2"></i>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Section -->
<div class="preview-section mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-eye me-2"></i>
                Preview Data
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Nama Bagian:</label>
                        <span class="info-value" id="preview_nama_bagian">-</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Keterangan:</label>
                        <span class="info-value" id="preview_keterangan">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change History Section -->
<div class="history-section mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>
                Riwayat Perubahan
            </h5>
        </div>
        <div class="card-body">
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Data Diperbarui</h6>
                        <p class="timeline-desc">Bagian berhasil diupdate oleh Admin</p>
                        <small class="timeline-time">15 Januari 2024, 14:30 WIB</small>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Data Dibuat</h6>
                        <p class="timeline-desc">Bagian baru berhasil ditambahkan ke sistem</p>
                        <small class="timeline-time">15 Januari 2024, 10:15 WIB</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Global variables
    let originalData = {};

    // DOM Content Loaded Event
    document.addEventListener('DOMContentLoaded', function() {
        loadData();
        setupEventListeners();
    });

    // Load Data
    function loadData() {
        // Simulate loading data from URL parameter or API
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id') || 1;
        
        // Sample data - in real app, this would come from API
        const sampleData = {
            1: {
                id: 1,
                nama_bagian: 'Sumber Daya Manusia',
                keterangan: 'Divisi yang mengelola kepegawaian dan pengembangan SDM'
            },
            2: {
                id: 2,
                nama_bagian: 'Keuangan',
                keterangan: 'Divisi yang mengelola keuangan dan akuntansi perusahaan'
            },
            3: {
                id: 3,
                nama_bagian: 'Pengadaan',
                keterangan: 'Divisi yang mengelola pengadaan barang dan jasa'
            }
        };

        const data = sampleData[id] || sampleData[1];
        originalData = { ...data };

        // Populate form
        document.getElementById('bagian_id').value = data.id;
        document.getElementById('nama_bagian').value = data.nama_bagian;
        document.getElementById('keterangan').value = data.keterangan;

        // Update preview
        updatePreview();
    }

    // Setup Event Listeners
    function setupEventListeners() {
        // Real-time preview
        const namaBagianInput = document.getElementById('nama_bagian');
        const keteranganInput = document.getElementById('keterangan');

        if (namaBagianInput) {
            namaBagianInput.addEventListener('input', updatePreview);
        }

        if (keteranganInput) {
            keteranganInput.addEventListener('input', updatePreview);
        }

        // Form validation
        const form = document.getElementById('editForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                updateData();
            });
        }
    }

    // Update Preview
    function updatePreview() {
        const namaBagian = document.getElementById('nama_bagian').value;
        const keterangan = document.getElementById('keterangan').value;

        document.getElementById('preview_nama_bagian').textContent = namaBagian || '-';
        document.getElementById('preview_keterangan').textContent = keterangan || '-';
    }

    // Update Data
    function updateData() {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);
        
        const namaBagian = formData.get('nama_bagian');
        const keterangan = formData.get('keterangan');

        // Validation
        if (!validateForm(namaBagian)) {
            return;
        }

        // Check if data has changed
        if (namaBagian === originalData.nama_bagian && keterangan === originalData.keterangan) {
            showAlert('Info', 'Tidak ada perubahan data', 'info');
            return;
        }

        // Show loading
        const updateBtn = document.querySelector('button[onclick="updateData()"]');
        const originalText = updateBtn.innerHTML;
        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengupdate...';
        updateBtn.disabled = true;

        // Simulate API call
        setTimeout(() => {
            // Reset button
            updateBtn.innerHTML = originalText;
            updateBtn.disabled = false;

            // Show success message
            showAlert('Sukses', 'Bagian berhasil diupdate', 'success');
            
            // Update original data
            originalData.nama_bagian = namaBagian;
            originalData.keterangan = keterangan;
            
            // Redirect after delay
            setTimeout(() => {
                goBack();
            }, 2000);
        }, 1500);
    }

    // Validate Form
    function validateForm(namaBagian) {
        let isValid = true;
        const namaBagianInput = document.getElementById('nama_bagian');

        // Clear previous validation
        namaBagianInput.classList.remove('is-invalid');

        if (!namaBagian.trim()) {
            namaBagianInput.classList.add('is-invalid');
            showAlert('Error', 'Nama bagian harus diisi', 'danger');
            isValid = false;
        } else if (namaBagian.length < 3) {
            namaBagianInput.classList.add('is-invalid');
            showAlert('Error', 'Nama bagian minimal 3 karakter', 'danger');
            isValid = false;
        }

        return isValid;
    }

    // Go Back
    function goBack() {
        if (confirm('Apakah Anda yakin ingin keluar? Perubahan yang belum disimpan akan hilang.')) {
            window.history.back();
        }
    }

    // Show Alert
    function showAlert(title, message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = `
            top: 20px; 
            right: 20px; 
            z-index: 9999; 
            min-width: 300px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.15); 
            border: none; 
            border-radius: 8px;
        `;

        let bgColor, textColor;
        switch(type) {
            case 'success':
                bgColor = '#d4edda';
                textColor = '#155724';
                break;
            case 'info':
                bgColor = '#d1ecf1';
                textColor = '#0c5460';
                break;
            case 'warning':
                bgColor = '#fff3cd';
                textColor = '#856404';
                break;
            case 'danger':
                bgColor = '#f8d7da';
                textColor = '#721c24';
                break;
            default:
                bgColor = '#d4edda';
                textColor = '#155724';
        }

        alertDiv.style.backgroundColor = bgColor;
        alertDiv.style.color = textColor;

        alertDiv.innerHTML = `
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>

<style>
.form-section .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.form-section .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0 !important;
    border: none;
}

.form-section .form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.form-section .form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-section .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-section .form-text {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 5px;
}

.form-actions {
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
    margin-top: 20px;
}

.preview-section .card,
.history-section .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.preview-section .card-header {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border-radius: 10px 10px 0 0 !important;
    border: none;
}

.history-section .card-header {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    border-radius: 10px 10px 0 0 !important;
    border: none;
}

.info-item {
    margin-bottom: 15px;
}

.info-label {
    font-weight: 600;
    color: #333;
    display: block;
    margin-bottom: 5px;
}

.info-value {
    color: #666;
    font-size: 1rem;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #667eea;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 1rem;
    font-weight: 600;
    color: #333;
}

.timeline-desc {
    margin: 0 0 5px 0;
    color: #666;
    font-size: 0.9rem;
}

.timeline-time {
    color: #999;
    font-size: 0.8rem;
}

.text-danger {
    color: #dc3545 !important;
}

.btn {
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6c757d;
    border: none;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}
</style>
@endpush
