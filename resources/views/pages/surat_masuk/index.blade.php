@extends('layouts.admin')

@push('head')
<link href="{{ asset('css/user.css') }}" rel="stylesheet">
@endpush

@section('admin-content')
<div class="page-header">
    @include('partials.page-title', [
        'title' => 'Manajemen Surat Masuk',
        'subtitle' => 'Kelola surat masuk instansi beserta lampiran dan detailnya.'
    ])
</div>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSuratMasuk">
        <i class="fas fa-plus"></i> Tambah Surat Masuk
    </button>
    <form class="d-flex" style="max-width:300px;" method="GET" action="{{ route('surat_masuk.index') }}">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari nomor/perihal..." value="{{ $query ?? '' }}">
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
        @if(isset($query) && $query)
            <a href="{{ route('surat_masuk.index') }}" class="btn btn-outline-danger ms-1" title="Clear search">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>
</div>

{{-- Filter Spesifik --}}
<div class="card mb-3">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Spesifik</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('surat_masuk.index') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filter_bagian" class="form-label">Bagian Tujuan</label>
                    <select name="filter_bagian" id="filter_bagian" class="form-select">
                        <option value="">Semua Bagian</option>
                        @foreach($bagian as $b)
                            <option value="{{ $b->id }}" {{ request('filter_bagian') == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bagian }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_sifat" class="form-label">Sifat Surat</label>
                    <select name="filter_sifat" id="filter_sifat" class="form-select">
                        <option value="">Semua Sifat</option>
                        <option value="Biasa" {{ request('filter_sifat') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                        <option value="Segera" {{ request('filter_sifat') == 'Segera' ? 'selected' : '' }}>Segera</option>
                        <option value="Penting" {{ request('filter_sifat') == 'Penting' ? 'selected' : '' }}>Penting</option>
                        <option value="Rahasia" {{ request('filter_sifat') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_tanggal_mulai" class="form-label">Tanggal Mulai</label>
                    <input type="date" name="filter_tanggal_mulai" id="filter_tanggal_mulai" class="form-control" value="{{ request('filter_tanggal_mulai') }}">
                </div>
                <div class="col-md-3">
                    <label for="filter_tanggal_akhir" class="form-label">Tanggal Akhir</label>
                    <input type="date" name="filter_tanggal_akhir" id="filter_tanggal_akhir" class="form-control" value="{{ request('filter_tanggal_akhir') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
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

@if(isset($query) && $query)
<div class="alert alert-info mb-3">
    <i class="fas fa-search me-2"></i>
    Hasil pencarian untuk: <strong>"{{ $query }}"</strong>
    - Ditemukan {{ $suratMasuk->count() }} surat masuk
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
    'footer' => view()->make('pages.surat_masuk._delete_modal._footer')->render(),
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
     * ANCHOR: Add Surat Masuk Handlers
     * Handle the add surat masuk form submission
     */
    const addSuratMasukHandlers = () => {
        const addSuratMasukForm = document.getElementById('addSuratMasukForm');
        const addSuratMasukSubmitBtn = document.getElementById('addSuratMasukSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        if (addSuratMasukForm) {
            addSuratMasukForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors(addSuratMasukForm);
                setLoadingState(true, addSuratMasukSubmitBtn);

                try {
                    const formData = new FormData(addSuratMasukForm);
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000);
                    const response = await fetchWithRetry(addSuratMasukForm.action, {
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
                        addSuratMasukForm.reset();
                        bootstrap.Modal.getInstance(document.getElementById('modalAddSuratMasuk')).hide();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        handleErrorResponse(data, addSuratMasukForm);
                    }
                } catch (error) {
                    handleErrorResponse(error, addSuratMasukForm);
                } finally {
                    setLoadingState(false, addSuratMasukSubmitBtn);
                }
            });
        }
    }

    /**
     * ANCHOR: Edit Surat Masuk Handlers
     * Handle the edit surat masuk form submission
     */
    const editSuratMasukHandlers = () => {
        const editSuratMasukForm = document.getElementById('editSuratMasukForm');
        const editSuratMasukSubmitBtn = document.getElementById('editSuratMasukSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        editSuratMasukForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(editSuratMasukForm);
            setLoadingState(true, editSuratMasukSubmitBtn);

            try {
                const formData = new FormData(editSuratMasukForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(editSuratMasukForm.action, {
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
                    editSuratMasukForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalEditSuratMasuk')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, editSuratMasukForm);
                }
            } catch (error) {
                handleErrorResponse(error, editSuratMasukForm);
            } finally {
                setLoadingState(false, editSuratMasukSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Delete Surat Masuk Handlers
     * Handle the delete surat masuk form submission
     */
    const deleteSuratMasukHandlers = () => {
        const deleteSuratMasukForm = document.getElementById('deleteSuratMasukForm');
        const deleteSuratMasukSubmitBtn = document.getElementById('deleteSuratMasukSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        deleteSuratMasukForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(deleteSuratMasukForm);
            setLoadingState(true, deleteSuratMasukSubmitBtn);

            try {
                const formData = new FormData(deleteSuratMasukForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(deleteSuratMasukForm.action, {
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
                    deleteSuratMasukForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalDeleteSuratMasuk')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, deleteSuratMasukForm);
                }
            } catch (error) {
                handleErrorResponse(error, deleteSuratMasukForm);
            } finally {
                setLoadingState(false, deleteSuratMasukSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Toggle Disposisi Fields
     * Toggle the visibility of disposisi fields based on checkbox
     */
    const toggleDisposisiFields = () => {
        const checkbox = document.getElementById('add_buat_disposisi');
        const fields = document.getElementById('add_disposisi_fields');
        
        if (checkbox.checked) {
            fields.style.display = 'block';
        } else {
            fields.style.display = 'none';
        }
    }

    // Add event listener for disposisi checkbox
    document.addEventListener('DOMContentLoaded', function() {
        const disposisiCheckbox = document.getElementById('add_buat_disposisi');
        if (disposisiCheckbox) {
            disposisiCheckbox.addEventListener('change', toggleDisposisiFields);
        }
    });

    /**
     * ANCHOR: Show Edit Surat Masuk Modal
     * Show the edit surat masuk modal
     * @param {number} suratMasukId - The id of the surat masuk to edit
     */
    const showEditSuratMasukModal = (suratMasukId) => {
        const editSuratMasukForm = document.getElementById('editSuratMasukForm');
        const idInput = document.getElementById('edit_surat_masuk_id');
        const nomorSuratInput = document.getElementById('edit_nomor_surat');
        const tanggalSuratInput = document.getElementById('edit_tanggal_surat');
        const tanggalTerimaInput = document.getElementById('edit_tanggal_terima');
        const perihalInput = document.getElementById('edit_perihal');
        const pengirimInput = document.getElementById('edit_pengirim');
        const sifatSuratInput = document.getElementById('edit_sifat_surat');
        const tujuanBagianInput = document.getElementById('edit_tujuan_bagian_id');
        const ringkasanIsiInput = document.getElementById('edit_ringkasan_isi');
        const keteranganInput = document.getElementById('edit_keterangan');

        const suratMasuk = suratMasukDataCurrentPage.data.find(surat => surat.id === suratMasukId);
        const { id, nomor_surat, tanggal_surat, tanggal_terima, perihal, pengirim, sifat_surat, tujuan_bagian_id, ringkasan_isi, keterangan } = suratMasuk;

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
        tujuanBagianInput.value = tujuan_bagian_id || '';
        ringkasanIsiInput.value = ringkasan_isi || '';
        keteranganInput.value = keterangan || '';

        editSuratMasukForm.action = `/surat-masuk/${id}`;
    }

    /**
     * ANCHOR: Show Delete Surat Masuk Modal
     * Show the delete surat masuk modal
     * @param {number} suratMasukId - The id of the surat masuk to delete
     */
    const showDeleteSuratMasukModal = (suratMasukId) => {
        const deleteSuratMasukName = document.getElementById('deleteSuratMasukName');
        const deleteSuratMasukForm = document.getElementById('deleteSuratMasukForm');

        const suratMasuk = suratMasukDataCurrentPage.data.find(surat => surat.id === suratMasukId);
        const { id, nomor_surat } = suratMasuk;

        deleteSuratMasukName.textContent = nomor_surat;
        deleteSuratMasukForm.action = `/surat-masuk/${id}`;
    }

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
    }

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

    addSuratMasukHandlers();
    editSuratMasukHandlers();
    deleteSuratMasukHandlers();
</script>
@endpush
