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
            <li class="breadcrumb-item active">Detail Bagian</li>
        </ol>
    </nav>
    <div class="page-title">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Detail Bagian</h2>
                <p class="page-subtitle mb-0">Informasi lengkap bagian/divisi</p>
            </div>
            <div>
                <a href="#" class="btn btn-secondary me-2" onclick="goBack()">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali
                </a>
                <a href="#" class="btn btn-primary" onclick="editData()">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Detail Information Section -->
<div class="detail-section">
    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Dasar
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <label class="info-label">Nama Bagian</label>
                                <div class="info-value" id="detail_nama_bagian">-</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <label class="info-label">Jumlah User</label>
                                <div class="info-value">
                                    <span class="badge badge-primary" id="detail_jumlah_user">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="info-item">
                                <label class="info-label">Keterangan</label>
                                <div class="info-value" id="detail_keterangan">-</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <label class="info-label">Tanggal Dibuat</label>
                                <div class="info-value" id="detail_tanggal_dibuat">-</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <label class="info-label">Terakhir Diupdate</label>
                                <div class="info-value" id="detail_tanggal_update">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User List Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Daftar User
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">
                                <tr>
                                    <td>1</td>
                                    <td><strong>admin_hrd</strong></td>
                                    <td>Admin HRD</td>
                                    <td>admin.hrd@company.com</td>
                                    <td><span class="badge badge-success">Admin</span></td>
                                    <td><span class="badge badge-success">Aktif</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><strong>staf_hrd_1</strong></td>
                                    <td>Staf HRD 1</td>
                                    <td>staf1.hrd@company.com</td>
                                    <td><span class="badge badge-info">Staf</span></td>
                                    <td><span class="badge badge-success">Aktif</span></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><strong>staf_hrd_2</strong></td>
                                    <td>Staf HRD 2</td>
                                    <td>staf2.hrd@company.com</td>
                                    <td><span class="badge badge-info">Staf</span></td>
                                    <td><span class="badge badge-warning">Nonaktif</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Statistics Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistik
                    </h5>
                </div>
                <div class="card-body">
                    <div class="stat-item">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" id="stat_surat_masuk">45</div>
                            <div class="stat-label">Surat Masuk</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" id="stat_surat_keluar">32</div>
                            <div class="stat-label">Surat Keluar</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" id="stat_disposisi">18</div>
                            <div class="stat-label">Disposisi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Aktivitas Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Surat Masuk Baru</div>
                                <div class="activity-desc">001/SM/XII/2024</div>
                                <div class="activity-time">2 jam yang lalu</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-info">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Data Diperbarui</div>
                                <div class="activity-desc">Informasi bagian diupdate</div>
                                <div class="activity-time">1 hari yang lalu</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-warning">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Disposisi Baru</div>
                                <div class="activity-desc">Surat disposisi ke bagian lain</div>
                                <div class="activity-time">2 hari yang lalu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Aksi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="editData()">
                            <i class="fas fa-edit me-2"></i>
                            Edit Bagian
                        </button>
                        <button class="btn btn-outline-success" onclick="viewUsers()">
                            <i class="fas fa-users me-2"></i>
                            Kelola User
                        </button>
                        <button class="btn btn-outline-info" onclick="viewReports()">
                            <i class="fas fa-chart-line me-2"></i>
                            Lihat Laporan
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteData()">
                            <i class="fas fa-trash me-2"></i>
                            Hapus Bagian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change History Section -->
<div class="history-section">
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
                        <p class="timeline-desc">Keterangan bagian diupdate oleh Admin</p>
                        <small class="timeline-time">15 Januari 2024, 14:30 WIB</small>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">User Ditambahkan</h6>
                        <p class="timeline-desc">Staf HRD 2 ditambahkan ke bagian ini</p>
                        <small class="timeline-time">12 Januari 2024, 09:15 WIB</small>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker bg-info"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Data Dibuat</h6>
                        <p class="timeline-desc">Bagian baru berhasil ditambahkan ke sistem</p>
                        <small class="timeline-time">10 Januari 2024, 08:00 WIB</small>
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
    let currentData = {};

    // DOM Content Loaded Event
    document.addEventListener('DOMContentLoaded', function() {
        loadData();
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
                keterangan: 'Divisi yang mengelola kepegawaian dan pengembangan SDM',
                jumlah_user: 8,
                tanggal_dibuat: '15 Januari 2024',
                tanggal_update: '15 Januari 2024',
                stat_surat_masuk: 45,
                stat_surat_keluar: 32,
                stat_disposisi: 18
            },
            2: {
                id: 2,
                nama_bagian: 'Keuangan',
                keterangan: 'Divisi yang mengelola keuangan dan akuntansi perusahaan',
                jumlah_user: 6,
                tanggal_dibuat: '15 Januari 2024',
                tanggal_update: '15 Januari 2024',
                stat_surat_masuk: 38,
                stat_surat_keluar: 28,
                stat_disposisi: 15
            },
            3: {
                id: 3,
                nama_bagian: 'Pengadaan',
                keterangan: 'Divisi yang mengelola pengadaan barang dan jasa',
                jumlah_user: 4,
                tanggal_dibuat: '15 Januari 2024',
                tanggal_update: '15 Januari 2024',
                stat_surat_masuk: 32,
                stat_surat_keluar: 25,
                stat_disposisi: 12
            }
        };

        const data = sampleData[id] || sampleData[1];
        currentData = { ...data };

        // Populate detail information
        document.getElementById('detail_nama_bagian').textContent = data.nama_bagian;
        document.getElementById('detail_keterangan').textContent = data.keterangan;
        document.getElementById('detail_jumlah_user').textContent = data.jumlah_user;
        document.getElementById('detail_tanggal_dibuat').textContent = data.tanggal_dibuat;
        document.getElementById('detail_tanggal_update').textContent = data.tanggal_update;

        // Populate statistics
        document.getElementById('stat_surat_masuk').textContent = data.stat_surat_masuk;
        document.getElementById('stat_surat_keluar').textContent = data.stat_surat_keluar;
        document.getElementById('stat_disposisi').textContent = data.stat_disposisi;
    }

    // Edit Data
    function editData() {
        const id = currentData.id;
        window.location.href = `?page=edit&id=${id}`;
    }

    // Go Back
    function goBack() {
        window.history.back();
    }

    // View Users
    function viewUsers() {
        showAlert('Info', 'Fitur kelola user akan segera tersedia', 'info');
    }

    // View Reports
    function viewReports() {
        showAlert('Info', 'Fitur laporan akan segera tersedia', 'info');
    }

    // Delete Data
    function deleteData() {
        if (currentData.jumlah_user > 0) {
            showAlert('Error', 'Tidak dapat menghapus bagian yang memiliki user', 'danger');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus bagian "${currentData.nama_bagian}"?`)) {
            showAlert('Sukses', 'Bagian berhasil dihapus', 'success');
            setTimeout(() => {
                goBack();
            }, 2000);
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
.detail-section .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.detail-section .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0 !important;
    border: none;
}

.info-item {
    margin-bottom: 20px;
}

.info-label {
    font-weight: 600;
    color: #333;
    display: block;
    margin-bottom: 8px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    color: #666;
    font-size: 1.1rem;
    font-weight: 500;
}

.stat-item {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-right: 15px;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
    line-height: 1;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
    margin-top: 2px;
}

.activity-list {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.activity-item:hover {
    background: #f8f9fa;
}

.activity-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    margin-right: 12px;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
    margin-bottom: 2px;
}

.activity-desc {
    color: #666;
    font-size: 0.8rem;
    margin-bottom: 2px;
}

.activity-time {
    color: #999;
    font-size: 0.75rem;
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

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-primary {
    background: #667eea;
    color: white;
}

.badge-success {
    background: #28a745;
    color: white;
}

.badge-info {
    background: #17a2b8;
    color: white;
}

.badge-warning {
    background: #ffc107;
    color: #212529;
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

.btn-outline-primary {
    border: 2px solid #667eea;
    color: #667eea;
}

.btn-outline-primary:hover {
    background: #667eea;
    border-color: #667eea;
    transform: translateY(-2px);
}

.btn-outline-success {
    border: 2px solid #28a745;
    color: #28a745;
}

.btn-outline-success:hover {
    background: #28a745;
    border-color: #28a745;
    transform: translateY(-2px);
}

.btn-outline-info {
    border: 2px solid #17a2b8;
    color: #17a2b8;
}

.btn-outline-info:hover {
    background: #17a2b8;
    border-color: #17a2b8;
    transform: translateY(-2px);
}

.btn-outline-danger {
    border: 2px solid #dc3545;
    color: #dc3545;
}

.btn-outline-danger:hover {
    background: #dc3545;
    border-color: #dc3545;
    transform: translateY(-2px);
}
</style>
@endpush
