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
            suratMasuk.user?.username || '-';
        
        // Audit information
        document.getElementById('detail-created-by').textContent = 
            suratMasuk.creator?.nama || '-';
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

        let disposisiHtml = '<div class="row">';
        
        disposisi.forEach((disp, index) => {
            const statusBadgeClass = 
                disp.status === 'Menunggu' ? 'bg-warning' :
                disp.status === 'Dikerjakan' ? 'bg-info' :
                disp.status === 'Selesai' ? 'bg-success' : 'bg-secondary';
            
            disposisiHtml += `
                <div class="col-md-6 mb-3">
                    <div class="card border">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">Disposisi ${index + 1}</h6>
                                <span class="badge ${statusBadgeClass}">${disp.status}</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Tujuan:</small>
                                <p class="mb-1 fw-semibold">${disp.tujuan_bagian?.nama_bagian || '-'}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Instruksi:</small>
                                <p class="mb-1">${disp.instruksi || '-'}</p>
                            </div>
                            ${disp.catatan ? `
                                <div class="mb-2">
                                    <small class="text-muted">Catatan:</small>
                                    <p class="mb-1">${disp.catatan}</p>
                                </div>
                            ` : ''}
                            <div class="text-muted">
                                <small>Dibuat: ${disp.created_at ? new Date(disp.created_at).toLocaleString('id-ID') : '-'}</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        disposisiHtml += '</div>';
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
</script>
@endpush
