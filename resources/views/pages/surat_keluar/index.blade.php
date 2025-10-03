@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    @include('partials.page-title', [
        'title' => 'Manajemen Surat Keluar',
        'subtitle' => 'Kelola surat keluar instansi beserta lampiran dan detailnya.'
    ])
</div>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSuratKeluar">
        Tambah Surat Keluar
    </button>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            <i class="fas fa-filter"></i> Filter Lanjutan
        </button>
        <form class="d-flex" style="max-width:300px;" method="GET" action="{{ route('surat_keluar.index') }}">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari nomor/perihal..." value="{{ $filters['query'] ?? '' }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            @if(isset($filters['query']) && $filters['query'])
                <a href="{{ route('surat_keluar.index') }}" class="btn btn-outline-danger ms-1" title="Clear search">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>
</div>

<!-- Advanced Filter Collapse -->
<div class="collapse mb-3 filter-collapse" id="filterCollapse">
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filter Lanjutan
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('surat_keluar.index') }}" id="filterForm" class="filter-form">
                <div class="row g-3">
                    <!-- Sifat Surat -->
                    <div class="col-md-4">
                        <label for="sifat_surat" class="form-label">Sifat Surat</label>
                        <select name="sifat_surat" class="form-select" id="sifat_surat">
                            <option value="">Semua Sifat</option>
                            <option value="Biasa" {{ ($filters['sifat_surat'] ?? '') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                            <option value="Segera" {{ ($filters['sifat_surat'] ?? '') == 'Segera' ? 'selected' : '' }}>Segera</option>
                            <option value="Penting" {{ ($filters['sifat_surat'] ?? '') == 'Penting' ? 'selected' : '' }}>Penting</option>
                            <option value="Rahasia" {{ ($filters['sifat_surat'] ?? '') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                        </select>
                    </div>
                    
                    <!-- Bagian Pengirim -->
                    @if(Auth::user()->role === 'Admin')
                    <div class="col-md-4">
                        <label for="bagian_id" class="form-label">Bagian Pengirim</label>
                        <select name="bagian_id" class="form-select" id="bagian_id">
                            <option value="">Semua Bagian</option>
                            @foreach($bagian as $b)
                                <option value="{{ $b->id }}" {{ ($filters['bagian_id'] ?? '') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bagian }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    {{-- Hidden input untuk Staff - auto-set ke bagian mereka --}}
                    <input type="hidden" name="bagian_id" value="{{ Auth::user()->bagian_id }}">
                    @endif
                    
                    <!-- Tanggal -->
                    <div class="col-md-4">
                        <label for="tanggal" class="form-label">Tanggal Surat</label>
                        <input type="date" name="tanggal" class="form-control" id="tanggal" value="{{ $filters['tanggal'] ?? '' }}">
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="applyFilterBtn">
                            <i class="fas fa-search"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('surat_keluar.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($filters['sifat_surat']) && $filters['sifat_surat'] || isset($filters['bagian_id']) && $filters['bagian_id'] || isset($filters['tanggal']) && $filters['tanggal'])
<div class="alert alert-info mb-3">
    <i class="fas fa-filter me-2"></i>
    <strong>Filter Aktif:</strong>
    @if(isset($filters['sifat_surat']) && $filters['sifat_surat'])
        <span class="badge bg-warning me-1">Sifat: {{ $filters['sifat_surat'] }}</span>
    @endif
    @if(isset($filters['bagian_id']) && $filters['bagian_id'])
        @php
            $selectedBagian = $bagian->where('id', $filters['bagian_id'])->first();
        @endphp
        <span class="badge bg-info me-1">Bagian: {{ $selectedBagian->nama_bagian ?? 'Unknown' }}</span>
    @endif
    @if(isset($filters['tanggal']) && $filters['tanggal'])
        <span class="badge bg-success me-1">Tanggal: {{ $filters['tanggal'] }}</span>
    @endif
    <span class="ms-2">Ditemukan {{ $suratKeluar->total() }} surat keluar</span>
</div>
@endif

@include('partials.table', [
    'tableId' => 'suratKeluarTable',
    'thead' => view()->make('pages.surat_keluar._table._head')->render(),
    'tbody' => view()->make('pages.surat_keluar._table._body', compact('suratKeluar'))->render(),
])

@include('partials.pagination', [
    'currentPage' => $suratKeluar->currentPage(),
    'totalPages' => $suratKeluar->lastPage(),
    'baseUrl' => route('surat_keluar.index'),
    'showInfo' => 'Menampilkan ' . $suratKeluar->firstItem() . '-' . $suratKeluar->lastItem() . ' dari ' . $suratKeluar->total() . ' surat keluar'
])

@include('partials.modal', [
    'id' => 'modalAddSuratKeluar',
    'size' => 'modal-xl',
    'title' => 'Tambah Surat Keluar',
    'body' => view('pages.surat_keluar._form_modal._add_form', compact('bagian'))->render(),
])

@include('partials.modal', [
    'id' => 'modalEditSuratKeluar',
    'size' => 'modal-xl',
    'title' => 'Edit Surat Keluar',
    'body' => view('pages.surat_keluar._form_modal._edit_form', compact('bagian'))->render(),
])

@include('partials.modal', [
    'type' => 'danger',
    'id' => 'modalDeleteSuratKeluar',
    'title' => 'Konfirmasi Hapus Surat Keluar',
    'size' => 'modal-md',
    'body' => view()->make('pages.surat_keluar._delete_modal._body')->render(),
])

@include('partials.modal', [
    'id' => 'modalDetailSuratKeluar',
    'size' => 'modal-xl',
    'title' => 'Detail Surat Keluar',
    'body' => view()->make('pages.surat_keluar._detail_modal._body')->render(),
    'footer' => view()->make('pages.surat_keluar._detail_modal._footer')->render(),
])
@endsection

@push('scripts')
<script>
    const suratKeluarDataCurrentPage = {!! json_encode($suratKeluar->items()) !!};

    /**
     * ANCHOR: Show Detail Surat Keluar Modal
     * Show the detail surat keluar modal
     * @param {number} suratKeluarId - The id of the surat keluar to show
     */
    const showDetailSuratKeluarModal = async (suratKeluarId) => {
        try {
            // Show loading state
            const lampiranContent = document.getElementById('detail-lampiran-content');
            lampiranContent.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <p>Memuat detail surat keluar...</p>
                </div>
            `;

            // Fetch detail data
            const csrfToken = (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                document.querySelector('input[name="_token"]')?.value
            );

            const response = await fetch(`/surat-keluar/${suratKeluarId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch detail data');
            }

            const data = await response.json();
            
            if (data.success) {
                populateDetailModal(data.suratKeluar);
            } else {
                throw new Error(data.message || 'Failed to load detail');
            }

        } catch (error) {
            console.error('Error loading detail:', error);
            const lampiranContent = document.getElementById('detail-lampiran-content');
            lampiranContent.innerHTML = `
                <div class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>Gagal memuat detail surat keluar</p>
                    <small class="text-muted">${error.message}</small>
                </div>
            `;
        }
    }

    /**
     * ANCHOR: Format Date for Display
     * Format date consistently across the application
     * @param {string} dateString - The date string to format
     * @returns {string} Formatted date string
     */
    const formatDateForDisplay = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    /**
     * ANCHOR: Format DateTime for Display
     * Format datetime consistently across the application
     * @param {string} dateString - The datetime string to format
     * @returns {string} Formatted datetime string
     */
    const formatDateTimeForDisplay = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    /**
     * ANCHOR: Populate Detail Modal
     * Populate the detail modal with surat keluar data
     * @param {Object} suratKeluar - The surat keluar data
     */
    const populateDetailModal = (suratKeluar) => {
        // Store current surat keluar ID for action buttons
        window.currentDetailSuratKeluarId = suratKeluar.id;
        
        // Basic information
        document.getElementById('detail-nomor-surat').textContent = suratKeluar.nomor_surat || '-';
        document.getElementById('detail-tanggal-surat').textContent = formatDateForDisplay(suratKeluar.tanggal_surat);
        document.getElementById('detail-tanggal-keluar').textContent = formatDateForDisplay(suratKeluar.tanggal_keluar);
        document.getElementById('detail-perihal').textContent = suratKeluar.perihal || '-';
        
        // Sifat surat dengan badge
        const sifatSuratElement = document.getElementById('detail-sifat-surat');
        const sifatSurat = suratKeluar.sifat_surat || 'Biasa';
        let badgeClass = 'badge-secondary';
        switch(sifatSurat) {
            case 'Segera':
                badgeClass = 'badge-warning';
                break;
            case 'Penting':
                badgeClass = 'badge-danger';
                break;
            case 'Rahasia':
                badgeClass = 'badge-dark';
                break;
            default:
                badgeClass = 'badge-secondary';
        }
        sifatSuratElement.innerHTML = `<span class="badge ${badgeClass}">${sifatSurat}</span>`;
        
        document.getElementById('detail-tujuan').textContent = suratKeluar.tujuan || '-';
        
        // Related information
        document.getElementById('detail-bagian-pengirim').textContent = 
            suratKeluar.pengirim_bagian?.nama_bagian || '-';
        
        // Audit information
        document.getElementById('detail-user').textContent = 
            suratKeluar.user?.username || '-';
        document.getElementById('detail-created-at').textContent = 
            formatDateTimeForDisplay(suratKeluar.created_at);
        document.getElementById('detail-updated-by').textContent = 
            suratKeluar.updater?.username || '-';
        document.getElementById('detail-updated-at').textContent = 
            formatDateTimeForDisplay(suratKeluar.updated_at);

        // Ringkasan isi
        const ringkasanSection = document.getElementById('detail-ringkasan-section');
        const ringkasanContent = document.getElementById('detail-ringkasan-isi');
        if (suratKeluar.ringkasan_isi) {
            ringkasanContent.textContent = suratKeluar.ringkasan_isi;
            ringkasanSection.style.display = 'block';
        } else {
            ringkasanSection.style.display = 'none';
        }

        // Keterangan
        const keteranganSection = document.getElementById('detail-keterangan-section');
        const keteranganContent = document.getElementById('detail-keterangan');
        if (suratKeluar.keterangan) {
            keteranganContent.textContent = suratKeluar.keterangan;
            keteranganSection.style.display = 'block';
        } else {
            keteranganSection.style.display = 'none';
        }

        // Lampiran
        populateLampiranDetail(suratKeluar.lampiran || []);
    }

    /**
     * ANCHOR: Edit from Detail Modal
     * Open edit modal from detail modal
     */
    const editFromDetail = () => {
        if (window.currentDetailSuratKeluarId) {
            // Close detail modal
            bootstrap.Modal.getInstance(document.getElementById('modalDetailSuratKeluar')).hide();
            
            // Open edit modal after a short delay
            setTimeout(() => {
                showEditSuratKeluarModal(window.currentDetailSuratKeluarId);
                bootstrap.Modal.getInstance(document.getElementById('modalEditSuratKeluar')).show();
            }, 300);
        }
    };

    /**
     * ANCHOR: Delete from Detail Modal
     * Open delete modal from detail modal
     */
    const deleteFromDetail = () => {
        if (window.currentDetailSuratKeluarId) {
            // Close detail modal
            bootstrap.Modal.getInstance(document.getElementById('modalDetailSuratKeluar')).hide();
            
            // Open delete modal after a short delay
            setTimeout(() => {
                showDeleteSuratKeluarModal(window.currentDetailSuratKeluarId);
                bootstrap.Modal.getInstance(document.getElementById('modalDeleteSuratKeluar')).show();
            }, 300);
        }
    };

    /**
     * ANCHOR: Get File Icon Class
     * Get appropriate icon class based on file extension
     * @param {string} fileName - The file name
     * @returns {string} Icon class string
     */
    const getFileIconClass = (fileName) => {
        const ext = fileName.toLowerCase().split('.').pop();
        switch(ext) {
            case 'pdf': return 'fa-file-pdf text-danger';
            case 'doc': case 'docx': return 'fa-file-word text-primary';
            case 'xls': case 'xlsx': return 'fa-file-excel text-success';
            case 'zip': case 'rar': return 'fa-file-archive text-warning';
            case 'jpg': case 'jpeg': case 'png': case 'gif': return 'fa-file-image text-info';
            case 'txt': return 'fa-file-alt text-secondary';
            default: return 'fa-file text-muted';
        }
    };

    /**
     * ANCHOR: Format File Size
     * Format file size in human readable format
     * @param {number} bytes - File size in bytes
     * @returns {string} Formatted file size
     */
    const formatFileSize = (bytes) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    /**
     * ANCHOR: Populate Lampiran Detail
     * Populate the lampiran section with attachment data
     * @param {Array} lampiran - Array of lampiran data
     */
    const populateLampiranDetail = (lampiran) => {
        const lampiranContent = document.getElementById('detail-lampiran-content');
        
        if (lampiran.length === 0) {
            lampiranContent.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-paperclip fa-2x mb-2"></i>
                    <p>Tidak ada lampiran</p>
                </div>
            `;
            return;
        }

        let lampiranHtml = '<div class="row">';
        
        lampiran.forEach((file, index) => {
            const iconClass = getFileIconClass(file.nama_file);
            const downloadUrl = `/storage/${file.path_file}`;
            const fileSize = file.file_size ? formatFileSize(file.file_size) : 'Unknown';
            const badgeClass = file.tipe_lampiran === 'utama' ? 'badge-primary' : 'badge-secondary';
            
            lampiranHtml += `
                <div class="col-md-6 mb-3">
                    <div class="card border">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas ${iconClass} fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1 text-truncate" title="${file.nama_file}">
                                        ${file.nama_file}
                                    </h6>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge ${badgeClass}">
                                            ${file.tipe_lampiran === 'utama' ? 'Lampiran Utama' : 'Dokumen Pendukung'}
                                        </span>
                                        <small class="text-muted">${fileSize}</small>
                                    </div>
                                    <small class="text-muted">
                                        ${file.created_at ? formatDateTimeForDisplay(file.created_at) : '-'}
                                    </small>
                                </div>
                                <div class="ms-2">
                                    <a href="${downloadUrl}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Download ${file.nama_file}"
                                       download="${file.nama_file}">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        lampiranHtml += '</div>';
        lampiranContent.innerHTML = lampiranHtml;
    }

    /**
     * ANCHOR: Simple Filter Handlers
     * Handle simple filter functionality - manual submit only
     */
    const simpleFilterHandlers = () => {
        const filterForm = document.getElementById('filterForm');
        const applyFilterBtn = document.getElementById('applyFilterBtn');
        
        // Add loading state to filter button
        filterForm.addEventListener('submit', function() {
            applyFilterBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menerapkan...';
            applyFilterBtn.disabled = true;
        });
        
        console.log('Filter form ready - manual submit only');
    }

    // Initialize simple filter handlers
    simpleFilterHandlers();
</script>
@endpush
