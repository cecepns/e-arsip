@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    @include('partials.page-title', [
        'title' => 'Manajemen Surat Masuk',
        'subtitle' => 'Kelola surat masuk instansi beserta lampiran dan detailnya.'
    ])
</div>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSuratMasuk">
        Tambah Surat Masuk
    </button>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            <i class="fas fa-filter"></i> Filter Lanjutan
        </button>
        <form class="d-flex" style="max-width:300px;" method="GET" action="{{ route('surat_masuk.index') }}">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari nomor/perihal..." value="{{ $filters['query'] ?? '' }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            @if(isset($filters['query']) && $filters['query'])
                <a href="{{ route('surat_masuk.index') }}" class="btn btn-outline-danger ms-1" title="Clear search">
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
            <form method="GET" action="{{ route('surat_masuk.index') }}" id="filterForm" class="filter-form">
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
                    
                    <!-- Bagian Tujuan -->
                    <div class="col-md-4">
                        <label for="bagian_id" class="form-label">Bagian Tujuan</label>
                        <select name="bagian_id" class="form-select" id="bagian_id">
                            <option value="">Semua Bagian</option>
                            @foreach($bagian as $b)
                                <option value="{{ $b->id }}" {{ ($filters['bagian_id'] ?? '') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bagian }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
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
                        <a href="{{ route('surat_masuk.index') }}" class="btn btn-outline-secondary">
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
    <span class="ms-2">Ditemukan {{ $suratMasuk->total() }} surat masuk</span>
</div>
@endif

@include('partials.table', [
    'tableId' => 'suratMasukTable',
    'thead' => view()->make('pages.surat_masuk._table._head')->render(),
    'tbody' => view()->make('pages.surat_masuk._table._body', compact('suratMasuk'))->render(),
])

@include('partials.pagination', [
    'currentPage' => $suratMasuk->currentPage(),
    'totalPages' => $suratMasuk->lastPage(),
    'baseUrl' => route('surat_masuk.index'),
    'showInfo' => 'Menampilkan ' . $suratMasuk->firstItem() . '-' . $suratMasuk->lastItem() . ' dari ' . $suratMasuk->total() . ' surat masuk'
])

@include('partials.modal', [
    'id' => 'modalAddSuratMasuk',
    'size' => 'modal-xl',
    'title' => 'Tambah Surat Masuk',
    'body' => view('pages.surat_masuk._form_modal._add_form', compact('bagian'))->render(),
])

@include('partials.modal', [
    'id' => 'modalEditSuratMasuk',
    'size' => 'modal-xl',
    'title' => 'Edit Surat Masuk',
    'body' => view('pages.surat_masuk._form_modal._edit_form', compact('bagian'))->render(),
])

@include('partials.modal', [
    'type' => 'danger',
    'id' => 'modalDeleteSuratMasuk',
    'title' => 'Konfirmasi Hapus Surat Masuk',
    'size' => 'modal-md',
    'body' => view()->make('pages.surat_masuk._delete_modal._body')->render(),
])

@include('partials.modal', [
    'id' => 'modalDetailSuratMasuk',
    'size' => 'modal-xl',
    'title' => 'Detail Surat Masuk',
    'body' => view()->make('pages.surat_masuk._detail_modal._body')->render(),
    'footer' => view()->make('pages.surat_masuk._detail_modal._footer')->render(),
])
@endsection

@push('scripts')
<script>
    const suratMasukDataCurrentPage = {!! json_encode($suratMasuk) !!};

    /**
     * ANCHOR: Show Detail Surat Masuk Modal
     * Show the detail surat masuk modal
     * @param {number} suratMasukId - The id of the surat masuk to show
     */
    const showDetailSuratMasukModal = async (suratMasukId) => {
        try {
            // Show loading state
            const lampiranContent = document.getElementById('detail-lampiran-content');
            lampiranContent.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <p>Memuat detail surat masuk...</p>
                </div>
            `;

            // Fetch detail data
            const csrfToken = (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                document.querySelector('input[name="_token"]')?.value
            );

            const response = await fetch(`/surat-masuk/${suratMasukId}`, {
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
                populateDetailModal(data.suratMasuk);
            } else {
                throw new Error(data.message || 'Failed to load detail');
            }

        } catch (error) {
            console.error('Error loading detail:', error);
            const lampiranContent = document.getElementById('detail-lampiran-content');
            lampiranContent.innerHTML = `
                <div class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>Gagal memuat detail surat masuk</p>
                    <small class="text-muted">${error.message}</small>
                </div>
            `;
        }
    }

    /**
     * ANCHOR: Populate Detail Modal
     * Populate the detail modal with surat masuk data
     * @param {Object} suratMasuk - The surat masuk data
     */
    const populateDetailModal = (suratMasuk) => {
        // Store current surat masuk ID for action buttons
        window.currentDetailSuratMasukId = suratMasuk.id;
        // ANCHOR: Store complete surat masuk data for disposisi access
        window.currentDetailSuratMasuk = suratMasuk;
        
        // Basic information
        document.getElementById('detail-nomor-surat').textContent = suratMasuk.nomor_surat || '-';
        document.getElementById('detail-tanggal-surat').textContent = suratMasuk.tanggal_surat ? 
            new Date(suratMasuk.tanggal_surat).toLocaleDateString('id-ID') : '-';
        document.getElementById('detail-tanggal-terima').textContent = suratMasuk.tanggal_terima ? 
            new Date(suratMasuk.tanggal_terima).toLocaleDateString('id-ID') : '-';
        document.getElementById('detail-perihal').textContent = suratMasuk.perihal || '-';
        document.getElementById('detail-pengirim').textContent = suratMasuk.pengirim || '-';
        document.getElementById('detail-sifat-surat').textContent = suratMasuk.sifat_surat || '-';
        
        // Related information
        document.getElementById('detail-bagian-tujuan').textContent = 
            suratMasuk.tujuan_bagian?.nama_bagian || '-';
        document.getElementById('detail-user').textContent = 
            suratMasuk.user?.nama || '-';
        
        // Audit information
        document.getElementById('detail-updated-by').textContent = 
            suratMasuk.updater?.nama || '-';
        
        // Timestamps
        document.getElementById('detail-created-at').textContent = suratMasuk.created_at ? 
            new Date(suratMasuk.created_at).toLocaleString('id-ID') : '-';
        document.getElementById('detail-updated-at').textContent = suratMasuk.updated_at ? 
            new Date(suratMasuk.updated_at).toLocaleString('id-ID') : '-';

        // Ringkasan isi
        const ringkasanSection = document.getElementById('detail-ringkasan-section');
        const ringkasanContent = document.getElementById('detail-ringkasan-isi');
        if (suratMasuk.ringkasan_isi) {
            ringkasanContent.textContent = suratMasuk.ringkasan_isi;
            ringkasanSection.style.display = 'block';
        } else {
            ringkasanSection.style.display = 'none';
        }

        // Keterangan
        const keteranganSection = document.getElementById('detail-keterangan-section');
        const keteranganContent = document.getElementById('detail-keterangan');
        if (suratMasuk.keterangan) {
            keteranganContent.textContent = suratMasuk.keterangan;
            keteranganSection.style.display = 'block';
        } else {
            keteranganSection.style.display = 'none';
        }

        // Lampiran
        populateLampiranDetail(suratMasuk.lampiran || []);
        
        // Disposisi
        populateDisposisiDetail(suratMasuk.disposisi || []);
    }

    /**
     * ANCHOR: Populate Disposisi Detail
     * Populate the disposisi section with disposisi data
     * @param {Array} disposisi - Array of disposisi data
     */
    const populateDisposisiDetail = (disposisi) => {
        const disposisiContent = document.getElementById('detail-disposisi-content');
        const disposisiSection = document.getElementById('detail-disposisi-section');
        
        if (disposisi.length === 0) {
            disposisiSection.style.display = 'none';
            return;
        }

        let disposisiHtml = '';
        
        disposisi.forEach((disp, index) => {
            const statusBadgeClass = 
                disp.status === 'Menunggu' ? 'bg-warning' :
                disp.status === 'Dikerjakan' ? 'bg-info' :
                disp.status === 'Selesai' ? 'bg-success' : 'bg-secondary';
            
            // Get kepala bagian information
            const kepalaBagianTujuan = disp.tujuan_bagian?.kepala_bagian?.nama || '-';
            // ANCHOR: Kepala Bagian Pengirim diambil dari bagian yang dituju di surat masuk
            const kepalaBagianPengirim = window.currentDetailSuratMasuk?.tujuan_bagian?.kepala_bagian?.nama || '-';
            
            disposisiHtml += `
                <div class="mb-4">
                    <div class="card border">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-share-alt me-2"></i>Disposisi ${index + 1}
                                </h6>
                                <span class="badge ${statusBadgeClass}">${disp.status}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Dibuat Oleh:</span>
                                            <span class="text-secondary">${disp.user?.nama || '-'}</span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Disposisi Dari:</span>
                                            <span class="text-secondary">${kepalaBagianPengirim} (${window.currentDetailSuratMasuk?.tujuan_bagian?.nama_bagian || '-'})</span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Dibuat Pada:</span>
                                            <span class="text-muted">${disp.created_at ? new Date(disp.created_at).toLocaleString('id-ID') : '-'}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Disposisi Kepada:</span>
                                            <span class="text-secondary">${kepalaBagianTujuan} (${disp.tujuan_bagian?.nama_bagian || '-'})</span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Instruksi:</span>
                                            <span class="text-dark">${disp.isi_instruksi || '-'}</span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Tanggal Disposisi:</span>
                                            <span class="text-secondary">${disp.tanggal_disposisi ? new Date(disp.tanggal_disposisi).toLocaleDateString('id-ID') : '-'}</span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Batas Waktu:</span>
                                            <span class="text-secondary">${disp.batas_waktu ? new Date(disp.batas_waktu).toLocaleDateString('id-ID') : '-'}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            ${disp.catatan ? `
                                <div class="mt-3 pt-3 border-top">
                                    <p class="mb-2">
                                        <span class="fw-semibold text-dark">Catatan:</span>
                                    </p>
                                    <div class="bg-light p-3 rounded">
                                        <p class="mb-0 text-dark">${disp.catatan}</p>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        disposisiContent.innerHTML = disposisiHtml;
        disposisiSection.style.display = 'block';
    }

    /**
     * ANCHOR: Delete from Detail Modal
     * Open delete modal from detail modal
     */
    const deleteFromDetail = () => {
        if (window.currentDetailSuratMasukId) {
            // Close detail modal
            bootstrap.Modal.getInstance(document.getElementById('modalDetailSuratMasuk')).hide();
            
            // Open delete modal after a short delay
            setTimeout(() => {
                showDeleteSuratMasukModal(window.currentDetailSuratMasukId);
                bootstrap.Modal.getInstance(document.getElementById('modalDeleteSuratMasuk')).show();
            }, 300);
        }
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
            const isPdf = file.nama_file.toLowerCase().endsWith('.pdf');
            const iconClass = isPdf ? 'fa-file-pdf text-danger' : 'fa-file-alt text-primary';
            const downloadUrl = `/storage/${file.path_file}`;
            
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
                                    <small class="text-muted">
                                        ${file.tipe_lampiran === 'utama' ? 'Lampiran Utama' : 'Dokumen Pendukung'}
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

    /**
     * ANCHOR: Show Edit Surat Masuk Modal
     * Show the edit surat masuk modal
     * @param {number} suratMasukId - The id of the surat masuk to edit
     */
    window.showEditSuratMasukModal = async (suratMasukId) => {
        const editSuratMasukForm = document.getElementById('editSuratMasukForm');
        const idInput = document.getElementById('edit_surat_masuk_id');
        const nomorSuratInput = document.getElementById('edit_nomor_surat');
        const tanggalSuratInput = document.getElementById('edit_tanggal_surat');
        const tanggalTerimaInput = document.getElementById('edit_tanggal_terima');
        const perihalInput = document.getElementById('edit_perihal');
        const pengirimInput = document.getElementById('edit_pengirim');
        const sifatSuratInput = document.getElementById('edit_sifat_surat');
        const tujuanBagianInput = document.getElementById('edit_tujuan_bagian_id');
        const editBagianDisplay = document.getElementById('edit_bagian_display');
        const ringkasanIsiInput = document.getElementById('edit_ringkasan_isi');
        const keteranganInput = document.getElementById('edit_keterangan');

        const suratMasuk = suratMasukDataCurrentPage.data.find(surat => surat.id === suratMasukId);
        const { id, nomor_surat, tanggal_surat, tanggal_terima, perihal, pengirim, sifat_surat, tujuan_bagian_id, ringkasan_isi, keterangan, tujuan_bagian } = suratMasuk;

        const formatDateForInput = (isoDate) => {
            if (!isoDate) return '';
            return new Date(isoDate).toISOString().split('T')[0];
        };

        idInput.value = id;
        nomorSuratInput.value = nomor_surat || '';
        tanggalSuratInput.value = formatDateForInput(tanggal_surat) || '';
        tanggalTerimaInput.value = formatDateForInput(tanggal_terima) || '';
        perihalInput.value = perihal || '';
        pengirimInput.value = pengirim || '';
        sifatSuratInput.value = sifat_surat || '';
        
        if (tujuanBagianInput) {
            tujuanBagianInput.value = tujuan_bagian_id || '';
        } else if (editBagianDisplay) {
            editBagianDisplay.textContent = tujuan_bagian?.nama_bagian || 'Bagian tidak ditemukan';
        }
        
        ringkasanIsiInput.value = ringkasan_isi || '';
        keteranganInput.value = keterangan || '';

        // Load disposisi data
        await loadDisposisiForEdit(id);

        editSuratMasukForm.action = `/surat-masuk/${id}`;
    }

    /**
     * ANCHOR: Load Disposisi for Edit
     * Load existing disposisi and display them as grid items (read-only)
     * @param {number} suratMasukId - The id of the surat masuk
     */
    window.loadDisposisiForEdit = async (suratMasukId) => {
        try {
            // Clear existing disposisi fields first
            if (window.editDisposisiManager) {
                window.editDisposisiManager.clearAllDisposisiFields();
            }
            
            // Get surat masuk data from current page data
            const suratMasuk = suratMasukDataCurrentPage.data.find(surat => surat.id === suratMasukId);
            
            if (suratMasuk && suratMasuk.disposisi && suratMasuk.disposisi.length > 0) {
                // Populate disposisi using the manager
                window.editDisposisiManager.populateDisposisi(suratMasuk.disposisi);
            }
        } catch (error) {
            console.error('Error loading disposisi for edit:', error);
            if (window.editDisposisiManager) {
                window.editDisposisiManager.toggleEmptyState();
            }
        }
    }

    /**
     * ANCHOR: Get Status Badge Class
     * Get the appropriate Bootstrap badge class for disposisi status
     * @param {string} status - The disposisi status
     * @returns {string} Bootstrap badge class
     */
    window.getStatusBadgeClass = (status) => {
        switch (status) {
            case 'Menunggu':
                return 'bg-warning';
            case 'Dikerjakan':
                return 'bg-primary';
            case 'Selesai':
                return 'bg-success';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * ANCHOR: Get Status Icon
     * Get the appropriate FontAwesome icon for disposisi status
     * @param {string} status - The disposisi status
     * @returns {string} FontAwesome icon class
     */
    window.getStatusIcon = (status) => {
        switch (status) {
            case 'Menunggu':
                return 'fas fa-clock';
            case 'Dikerjakan':
                return 'fas fa-play';
            case 'Selesai':
                return 'fas fa-check';
            default:
                return 'fas fa-question';
        }
    }

    // ========================================
    // ANCHOR: REUSABLE DISPOSISI FUNCTIONS
    // ========================================
    
    /**
     * ANCHOR: Disposisi Manager Class
     * Manages disposisi functionality for different forms
     */
    class DisposisiManager {
        constructor(formPrefix = '') {
            this.formPrefix = formPrefix;
            this.containerId = `${formPrefix}disposisi_container`;
            this.emptyStateId = `${formPrefix}disposisi_empty_state`;
            this.addButtonId = `${formPrefix}add_disposisi_btn`;
        }

        /**
         * Get container element
         */
        getContainer() {
            return document.getElementById(this.containerId);
        }

        /**
         * Get empty state element
         */
        getEmptyState() {
            return document.getElementById(this.emptyStateId);
        }

        /**
         * Get add button element
         */
        getAddButton() {
            return document.getElementById(this.addButtonId);
        }

        /**
         * Toggle Empty State
         * Show/hide empty state based on disposisi count
         */
        toggleEmptyState() {
            const emptyState = this.getEmptyState();
            const container = this.getContainer();
            const disposisiCount = container ? container.querySelectorAll('.disposisi-item').length : 0;
            
            if (emptyState && container) {
                if (disposisiCount === 0) {
                    emptyState.style.display = 'block';
                } else {
                    emptyState.style.display = 'none';
                }
            }
        }

        /**
         * Add Disposisi Field
         * Add a new disposisi field dynamically
         */
        addDisposisiField() {
            const container = this.getContainer();
            if (!container) return;

            const disposisiCount = container.querySelectorAll('.disposisi-item').length;
            const disposisiIndex = disposisiCount;
            
            const disposisiHtml = `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card disposisi-item h-100" data-index="${disposisiIndex}">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-share-alt me-2"></i>Disposisi ${disposisiIndex + 1}
                            </h6>
                            <button type="button" class="btn btn-danger btn-sm remove-disposisi" data-index="${disposisiIndex}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Tujuan Disposisi</label>
                                <select name="disposisi[${disposisiIndex}][tujuan_bagian_id]" class="form-select disposisi-tujuan" required>
                                    <option value="">Pilih Bagian Tujuan</option>
                                    @foreach($bagian ?? [] as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama_bagian }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="disposisi[${disposisiIndex}][status]" class="form-select disposisi-status" required>
                                    <option value="Menunggu">Menunggu</option>
                                    <option value="Dikerjakan">Dikerjakan</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Instruksi</label>
                                <textarea name="disposisi[${disposisiIndex}][instruksi]" class="form-control disposisi-instruksi" rows="3" placeholder="Instruksi untuk bagian tujuan" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea name="disposisi[${disposisiIndex}][catatan]" class="form-control disposisi-catatan" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Disposisi</label>
                                        <input type="date" name="disposisi[${disposisiIndex}][tanggal_disposisi]" class="form-control disposisi-tanggal-disposisi" placeholder="Tanggal disposisi">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Batas Waktu</label>
                                        <input type="date" name="disposisi[${disposisiIndex}][batas_waktu]" class="form-control disposisi-batas-waktu" placeholder="Batas waktu">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', disposisiHtml);
            this.updateDisposisiNumbers();
            this.toggleEmptyState();
        }

        /**
         * Remove Disposisi Field
         * Remove a specific disposisi field
         * @param {number} index - The index of the disposisi field to remove
         */
        removeDisposisiField(index) {
            const container = this.getContainer();
            if (!container) return;

            const disposisiItem = container.querySelector(`[data-index="${index}"]`);
            if (disposisiItem) {
                disposisiItem.remove();
                this.updateDisposisiNumbers();
                this.toggleEmptyState();
            }
        }

        /**
         * Clear All Disposisi Fields
         * Clear all disposisi fields from the container
         */
        clearAllDisposisiFields() {
            const container = this.getContainer();
            if (container) {
                container.innerHTML = '';
                this.toggleEmptyState();
            }
        }

        /**
         * Update Disposisi Numbers
         * Update the numbering of disposisi fields
         */
        updateDisposisiNumbers() {
            const container = this.getContainer();
            if (!container) return;

            const disposisiItems = container.querySelectorAll('.disposisi-item');
            disposisiItems.forEach((item, index) => {
                const title = item.querySelector('h6');
                const removeBtn = item.querySelector('.remove-disposisi');
                
                if (title) {
                    title.innerHTML = `<i class="fas fa-share-alt me-2"></i>Disposisi ${index + 1}`;
                }
                
                if (removeBtn) {
                    removeBtn.setAttribute('data-index', index);
                }
                
                // Update data-index
                item.setAttribute('data-index', index);
                
                // Update form field names
                const tujuanSelect = item.querySelector('.disposisi-tujuan');
                const statusSelect = item.querySelector('.disposisi-status');
                const instruksiTextarea = item.querySelector('.disposisi-instruksi');
                const catatanTextarea = item.querySelector('.disposisi-catatan');
                const tanggalDisposisiInput = item.querySelector('.disposisi-tanggal-disposisi');
                const batasWaktuInput = item.querySelector('.disposisi-batas-waktu');
                
                if (tujuanSelect) tujuanSelect.name = `disposisi[${index}][tujuan_bagian_id]`;
                if (statusSelect) statusSelect.name = `disposisi[${index}][status]`;
                if (instruksiTextarea) instruksiTextarea.name = `disposisi[${index}][instruksi]`;
                if (catatanTextarea) catatanTextarea.name = `disposisi[${index}][catatan]`;
                if (tanggalDisposisiInput) tanggalDisposisiInput.name = `disposisi[${index}][tanggal_disposisi]`;
                if (batasWaktuInput) batasWaktuInput.name = `disposisi[${index}][batas_waktu]`;
            });
        }

        /**
         * Populate existing disposisi data
         * @param {Array} disposisiData - Array of disposisi objects
         */
        populateDisposisi(disposisiData = []) {
            this.clearAllDisposisiFields();
            
            disposisiData.forEach((disposisi, index) => {
                this.addDisposisiField();
                
                // Fill the form fields
                const container = this.getContainer();
                const disposisiItem = container.querySelector(`[data-index="${index}"]`);
                
                if (disposisiItem) {
                    const tujuanSelect = disposisiItem.querySelector('.disposisi-tujuan');
                    const statusSelect = disposisiItem.querySelector('.disposisi-status');
                    const instruksiTextarea = disposisiItem.querySelector('.disposisi-instruksi');
                    const catatanTextarea = disposisiItem.querySelector('.disposisi-catatan');
                    const tanggalDisposisiInput = disposisiItem.querySelector('.disposisi-tanggal-disposisi');
                    const batasWaktuInput = disposisiItem.querySelector('.disposisi-batas-waktu');
                    
                    if (tujuanSelect && disposisi.tujuan_bagian_id) {
                        tujuanSelect.value = disposisi.tujuan_bagian_id;
                    }
                    if (statusSelect && disposisi.status) {
                        statusSelect.value = disposisi.status;
                    }
                    if (instruksiTextarea && disposisi.isi_instruksi) {
                        instruksiTextarea.value = disposisi.isi_instruksi;
                    }
                    if (catatanTextarea && disposisi.catatan) {
                        catatanTextarea.value = disposisi.catatan;
                    }
                    // Format tanggal to 'YYYY-MM-DD' for input[type="date"]
                    if (tanggalDisposisiInput && disposisi.tanggal_disposisi) {
                        const tanggal = new Date(disposisi.tanggal_disposisi);
                        tanggalDisposisiInput.value = !isNaN(tanggal) ? tanggal.toISOString().slice(0, 10) : '';
                    }
                    if (batasWaktuInput && disposisi.batas_waktu) {
                        const batas = new Date(disposisi.batas_waktu);
                        batasWaktuInput.value = !isNaN(batas) ? batas.toISOString().slice(0, 10) : '';
                    }
                }
            });
        }

        /**
         * Initialize disposisi functionality
         */
        initialize() {
            // Initialize empty state
            this.toggleEmptyState();
            
            // Add event listener for add disposisi button
            const addButton = this.getAddButton();
            if (addButton) {
                addButton.addEventListener('click', () => this.addDisposisiField());
            }

            // Add event delegation for remove buttons
            const container = this.getContainer();
            if (container) {
                container.addEventListener('click', (e) => {
                    if (e.target.closest('.remove-disposisi')) {
                        const button = e.target.closest('.remove-disposisi');
                        const index = parseInt(button.getAttribute('data-index'));
                        this.removeDisposisiField(index);
                    }
                });
            }
        }
    }

    // Global disposisi manager instance
    window.disposisiManager = new DisposisiManager();

    // Legacy functions for backward compatibility
    window.toggleEmptyState = () => window.disposisiManager.toggleEmptyState();
    window.addDisposisiField = () => window.disposisiManager.addDisposisiField();
    window.removeDisposisiField = (index) => window.disposisiManager.removeDisposisiField(index);
    window.clearAllDisposisiFields = () => window.disposisiManager.clearAllDisposisiFields();
    window.updateDisposisiNumbers = () => window.disposisiManager.updateDisposisiNumbers();
</script>
@endpush
