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
            <li class="breadcrumb-item active">Data Bagian</li>
        </ol>
    </nav>
    <div class="page-title">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Data Bagian</h2>
                <p class="page-subtitle mb-0">Kelola data master bagian/divisi organisasi</p>
            </div>
            <div>
                <button class="btn btn-primary" onclick="showCreateModal()">
                    <i class="fas fa-plus me-2"></i>
                    Tambah Bagian
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Data Bagian Section -->
<div class="data-section">
    <div class="data-header-section">
        <div class="data-title-wrapper">
            <h3 class="data-title">
                <i class="fas fa-building"></i>
                Daftar Bagian
            </h3>
        </div>
        <div class="data-controls">
            <div class="show-entries">
                <label>Tampilkan:</label>
                <select class="entries-select" id="entriesSelect">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Cari bagian..." id="searchInput">
                <i class="fas fa-search search-icon"></i>
            </div>
            <button class="export-btn" onclick="exportData()">
                <i class="fas fa-download"></i>
                Export
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Bagian</th>
                    <th>Keterangan</th>
                    <th>Jumlah User</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
                <tr class="data-row">
                    <td>1</td>
                    <td><strong>Sumber Daya Manusia</strong></td>
                    <td>Divisi yang mengelola kepegawaian dan pengembangan SDM</td>
                    <td><span class="badge badge-primary">8</span></td>
                    <td>15 Januari 2024</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat Detail" onclick="viewDetail(1)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" title="Edit" onclick="editData(1)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Hapus" onclick="deleteData(1)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="data-row">
                    <td>2</td>
                    <td><strong>Keuangan</strong></td>
                    <td>Divisi yang mengelola keuangan dan akuntansi perusahaan</td>
                    <td><span class="badge badge-primary">6</span></td>
                    <td>15 Januari 2024</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat Detail" onclick="viewDetail(2)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" title="Edit" onclick="editData(2)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Hapus" onclick="deleteData(2)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="data-row">
                    <td>3</td>
                    <td><strong>Pengadaan</strong></td>
                    <td>Divisi yang mengelola pengadaan barang dan jasa</td>
                    <td><span class="badge badge-primary">4</span></td>
                    <td>15 Januari 2024</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat Detail" onclick="viewDetail(3)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" title="Edit" onclick="editData(3)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Hapus" onclick="deleteData(3)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="data-row">
                    <td>4</td>
                    <td><strong>Sekretariat</strong></td>
                    <td>Divisi yang mengelola administrasi dan sekretariat</td>
                    <td><span class="badge badge-primary">5</span></td>
                    <td>15 Januari 2024</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat Detail" onclick="viewDetail(4)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" title="Edit" onclick="editData(4)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Hapus" onclick="deleteData(4)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="data-row">
                    <td>5</td>
                    <td><strong>Teknologi Informasi</strong></td>
                    <td>Divisi yang mengelola sistem informasi dan teknologi</td>
                    <td><span class="badge badge-primary">3</span></td>
                    <td>15 Januari 2024</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat Detail" onclick="viewDetail(5)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" title="Edit" onclick="editData(5)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Hapus" onclick="deleteData(5)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="table-footer">
        <div class="showing-info">
            <span>Menampilkan 1-5 dari 5 entries</span>
        </div>
        <div class="pagination-wrapper">
            <nav>
                <ul class="pagination">
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">Next</span>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">
                    <i class="fas fa-plus me-2"></i>
                    Tambah Bagian Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createForm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nama_bagian" class="form-label">Nama Bagian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_bagian" name="nama_bagian" required>
                            <div class="invalid-feedback">
                                Nama bagian harus diisi
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Deskripsi atau penjelasan tentang bagian ini"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveData()">
                    <i class="fas fa-save me-2"></i>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Edit Bagian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_nama_bagian" class="form-label">Nama Bagian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_bagian" name="nama_bagian" required>
                            <div class="invalid-feedback">
                                Nama bagian harus diisi
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3" placeholder="Deskripsi atau penjelasan tentang bagian ini"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="updateData()">
                    <i class="fas fa-save me-2"></i>
                    Update
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-eye me-2"></i>
                    Detail Bagian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama Bagian</label>
                        <p class="form-control-plaintext" id="detail_nama_bagian">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jumlah User</label>
                        <p class="form-control-plaintext" id="detail_jumlah_user">-</p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Keterangan</label>
                        <p class="form-control-plaintext" id="detail_keterangan">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Dibuat</label>
                        <p class="form-control-plaintext" id="detail_tanggal_dibuat">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Terakhir Diupdate</label>
                        <p class="form-control-plaintext" id="detail_tanggal_update">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="editFromDetail()">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Global variables
    let originalTableData = [];
    let currentEditId = null;

    // Sample data
    const sampleData = [
        {
            id: 1,
            nama_bagian: 'Sumber Daya Manusia',
            keterangan: 'Divisi yang mengelola kepegawaian dan pengembangan SDM',
            jumlah_user: 8,
            tanggal_dibuat: '15 Januari 2024',
            tanggal_update: '15 Januari 2024'
        },
        {
            id: 2,
            nama_bagian: 'Keuangan',
            keterangan: 'Divisi yang mengelola keuangan dan akuntansi perusahaan',
            jumlah_user: 6,
            tanggal_dibuat: '15 Januari 2024',
            tanggal_update: '15 Januari 2024'
        },
        {
            id: 3,
            nama_bagian: 'Pengadaan',
            keterangan: 'Divisi yang mengelola pengadaan barang dan jasa',
            jumlah_user: 4,
            tanggal_dibuat: '15 Januari 2024',
            tanggal_update: '15 Januari 2024'
        },
        {
            id: 4,
            nama_bagian: 'Sekretariat',
            keterangan: 'Divisi yang mengelola administrasi dan sekretariat',
            jumlah_user: 5,
            tanggal_dibuat: '15 Januari 2024',
            tanggal_update: '15 Januari 2024'
        },
        {
            id: 5,
            nama_bagian: 'Teknologi Informasi',
            keterangan: 'Divisi yang mengelola sistem informasi dan teknologi',
            jumlah_user: 3,
            tanggal_dibuat: '15 Januari 2024',
            tanggal_update: '15 Januari 2024'
        }
    ];

    // DOM Content Loaded Event
    document.addEventListener('DOMContentLoaded', function() {
        initializeTableData();
        setupEventListeners();
    });

    // Initialize Table Data
    function initializeTableData() {
        originalTableData = [...sampleData];
    }

    // Setup Event Listeners
    function setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterTable(this.value);
            });
        }

        // Entries select
        const entriesSelect = document.getElementById('entriesSelect');
        if (entriesSelect) {
            entriesSelect.addEventListener('change', function() {
                updateTableEntries(this.value);
            });
        }
    }

    // Filter Table
    function filterTable(searchTerm) {
        const tableBody = document.getElementById('dataTableBody');
        if (!tableBody) return;
        
        const searchLower = searchTerm.toLowerCase();
        tableBody.innerHTML = '';

        const filteredData = originalTableData.filter(item => {
            return item.nama_bagian.toLowerCase().includes(searchLower) ||
                    item.keterangan.toLowerCase().includes(searchLower);
        });

        filteredData.forEach((item, index) => {
            const row = createTableRow(item, index + 1);
            tableBody.appendChild(row);
        });

        updateShowingInfo(filteredData.length);
    }

    // Update Table Entries
    function updateTableEntries(count) {
        const tableBody = document.getElementById('dataTableBody');
        const searchInput = document.getElementById('searchInput');
        if (!tableBody || !searchInput) return;
        
        const currentSearch = searchInput.value.toLowerCase();
        
        let dataToShow = originalTableData;
        if (currentSearch) {
            dataToShow = originalTableData.filter(item => {
                return item.nama_bagian.toLowerCase().includes(currentSearch) ||
                        item.keterangan.toLowerCase().includes(currentSearch);
            });
        }

        tableBody.innerHTML = '';

        const limitedData = dataToShow.slice(0, parseInt(count));
        limitedData.forEach((item, index) => {
            const row = createTableRow(item, index + 1);
            tableBody.appendChild(row);
        });

        updateShowingInfo(limitedData.length, dataToShow.length);
    }

    // Create Table Row
    function createTableRow(item, index) {
        const row = document.createElement('tr');
        row.className = 'data-row';
        row.innerHTML = `
            <td>${index}</td>
            <td><strong>${item.nama_bagian}</strong></td>
            <td>${item.keterangan}</td>
            <td><span class="badge badge-primary">${item.jumlah_user}</span></td>
            <td>${item.tanggal_dibuat}</td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn view-btn" title="Lihat Detail" onclick="viewDetail(${item.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn edit-btn" title="Edit" onclick="editData(${item.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete-btn" title="Hapus" onclick="deleteData(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        return row;
    }

    // Update Showing Info
    function updateShowingInfo(showing, total = originalTableData.length) {
        const showingInfo = document.querySelector('.showing-info');
        if (showingInfo) {
            showingInfo.textContent = `Menampilkan 1-${showing} dari ${total} entries`;
        }
    }

    // Show Create Modal
    function showCreateModal() {
        const modal = new bootstrap.Modal(document.getElementById('createModal'));
        document.getElementById('createForm').reset();
        modal.show();
    }

    // Save Data
    function saveData() {
        const form = document.getElementById('createForm');
        const formData = new FormData(form);
        
        const namaBagian = formData.get('nama_bagian');
        const keterangan = formData.get('keterangan');

        if (!namaBagian.trim()) {
            showAlert('Error', 'Nama bagian harus diisi', 'danger');
            return;
        }

        // Check if nama bagian already exists
        const exists = originalTableData.some(item => 
            item.nama_bagian.toLowerCase() === namaBagian.toLowerCase()
        );

        if (exists) {
            showAlert('Error', 'Nama bagian sudah ada', 'danger');
            return;
        }

        // Add new data
        const newId = Math.max(...originalTableData.map(item => item.id)) + 1;
        const newData = {
            id: newId,
            nama_bagian: namaBagian,
            keterangan: keterangan || '-',
            jumlah_user: 0,
            tanggal_dibuat: new Date().toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }),
            tanggal_update: new Date().toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })
        };

        originalTableData.push(newData);
        refreshTable();
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('createModal'));
        modal.hide();
        
        showAlert('Sukses', 'Bagian berhasil ditambahkan', 'success');
    }

    // Edit Data
    function editData(id) {
        const data = originalTableData.find(item => item.id === id);
        if (!data) return;

        currentEditId = id;
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nama_bagian').value = data.nama_bagian;
        document.getElementById('edit_keterangan').value = data.keterangan;

        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    }

    // Update Data
    function updateData() {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);
        
        const namaBagian = formData.get('nama_bagian');
        const keterangan = formData.get('keterangan');

        if (!namaBagian.trim()) {
            showAlert('Error', 'Nama bagian harus diisi', 'danger');
            return;
        }

        // Check if nama bagian already exists (excluding current item)
        const exists = originalTableData.some(item => 
            item.id !== currentEditId && 
            item.nama_bagian.toLowerCase() === namaBagian.toLowerCase()
        );

        if (exists) {
            showAlert('Error', 'Nama bagian sudah ada', 'danger');
            return;
        }

        // Update data
        const dataIndex = originalTableData.findIndex(item => item.id === currentEditId);
        if (dataIndex !== -1) {
            originalTableData[dataIndex].nama_bagian = namaBagian;
            originalTableData[dataIndex].keterangan = keterangan || '-';
            originalTableData[dataIndex].tanggal_update = new Date().toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        refreshTable();
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();
        
        showAlert('Sukses', 'Bagian berhasil diupdate', 'success');
    }

    // View Detail
    function viewDetail(id) {
        const data = originalTableData.find(item => item.id === id);
        if (!data) return;

        document.getElementById('detail_nama_bagian').textContent = data.nama_bagian;
        document.getElementById('detail_keterangan').textContent = data.keterangan;
        document.getElementById('detail_jumlah_user').textContent = data.jumlah_user;
        document.getElementById('detail_tanggal_dibuat').textContent = data.tanggal_dibuat;
        document.getElementById('detail_tanggal_update').textContent = data.tanggal_update;

        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    }

    // Edit From Detail
    function editFromDetail() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
        modal.hide();
        
        setTimeout(() => {
            const namaBagian = document.getElementById('detail_nama_bagian').textContent;
            const data = originalTableData.find(item => item.nama_bagian === namaBagian);
            if (data) {
                editData(data.id);
            }
        }, 300);
    }

    // Delete Data
    function deleteData(id) {
        const data = originalTableData.find(item => item.id === id);
        if (!data) return;

        if (data.jumlah_user > 0) {
            showAlert('Error', 'Tidak dapat menghapus bagian yang memiliki user', 'danger');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus bagian "${data.nama_bagian}"?`)) {
            originalTableData = originalTableData.filter(item => item.id !== id);
            refreshTable();
            showAlert('Sukses', 'Bagian berhasil dihapus', 'success');
        }
    }

    // Refresh Table
    function refreshTable() {
        const tableBody = document.getElementById('dataTableBody');
        const searchInput = document.getElementById('searchInput');
        const entriesSelect = document.getElementById('entriesSelect');
        
        if (searchInput.value) {
            filterTable(searchInput.value);
        } else {
            updateTableEntries(entriesSelect.value);
        }
    }

    // Export Data
    function exportData() {
        showAlert('Info', 'Fitur export akan segera tersedia', 'info');
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
@endpush
