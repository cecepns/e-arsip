@extends('layouts.admin')

@push('head')
<link href="{{ asset('css/user.css') }}" rel="stylesheet">
@endpush

@section('admin-content')
<div class="page-header">
    @include('partials.page-title', [
        'title' => 'Manajemen Surat Keluar',
        'subtitle' => 'Kelola surat keluar instansi beserta lampiran dan detailnya.'
    ])
</div>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSuratKeluar">
        <i class="fas fa-plus"></i> Tambah Surat Keluar
    </button>
    <form class="d-flex" style="max-width:300px;" method="GET" action="{{ route('surat_keluar.index') }}">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari nomor/perihal..." value="{{ $query ?? '' }}">
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
        @if(isset($query) && $query)
            <a href="{{ route('surat_keluar.index') }}" class="btn btn-outline-danger ms-1" title="Clear search">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>
</div>

@if(isset($query) && $query)
<div class="alert alert-info mb-3">
    <i class="fas fa-search me-2"></i>
    Hasil pencarian untuk: <strong>"{{ $query }}"</strong>
    - Ditemukan {{ $suratKeluar->count() }} surat keluar
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
    'footer' => view()->make('pages.surat_keluar._delete_modal._footer')->render(),
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
    const suratKeluarDataCurrentPage = {!! json_encode($suratKeluar) !!};


    /**
     * ANCHOR: Edit Surat Keluar Handlers
     * Handle the edit surat keluar form submission
     */
    const editSuratKeluarHandlers = () => {
        const editSuratKeluarForm = document.getElementById('editSuratKeluarForm');
        const editSuratKeluarSubmitBtn = document.getElementById('editSuratKeluarSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        editSuratKeluarForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(editSuratKeluarForm);
            setLoadingState(true, editSuratKeluarSubmitBtn);

            try {
                const formData = new FormData(editSuratKeluarForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(editSuratKeluarForm.action, {
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
                    editSuratKeluarForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalEditSuratKeluar')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, editSuratKeluarForm);
                }
            } catch (error) {
                handleErrorResponse(error, editSuratKeluarForm);
            } finally {
                setLoadingState(false, editSuratKeluarSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Delete Surat Keluar Handlers
     * Handle the delete surat keluar form submission
     */
    const deleteSuratKeluarHandlers = () => {
        const deleteSuratKeluarForm = document.getElementById('deleteSuratKeluarForm');
        const deleteSuratKeluarSubmitBtn = document.getElementById('deleteSuratKeluarSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        deleteSuratKeluarForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(deleteSuratKeluarForm);
            setLoadingState(true, deleteSuratKeluarSubmitBtn);

            try {
                const formData = new FormData(deleteSuratKeluarForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(deleteSuratKeluarForm.action, {
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
                    deleteSuratKeluarForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalDeleteSuratKeluar')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, deleteSuratKeluarForm);
                }
            } catch (error) {
                handleErrorResponse(error, deleteSuratKeluarForm);
            } finally {
                setLoadingState(false, deleteSuratKeluarSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Show Edit Surat Keluar Modal
     * Show the edit surat keluar modal
     * @param {number} suratKeluarId - The id of the surat keluar to edit
     */
    const showEditSuratKeluarModal = (suratKeluarId) => {
        const editSuratKeluarForm = document.getElementById('editSuratKeluarForm');
        const idInput = document.getElementById('edit_surat_keluar_id');
        const nomorSuratInput = document.getElementById('edit_nomor_surat');
        const tanggalSuratInput = document.getElementById('edit_tanggal_surat');
        const tanggalKeluarInput = document.getElementById('edit_tanggal_keluar');
        const perihalInput = document.getElementById('edit_perihal');
        const tujuanInput = document.getElementById('edit_tujuan');
        const sifatSuratInput = document.getElementById('edit_sifat_surat');
        const pengirimBagianInput = document.getElementById('edit_pengirim_bagian_id');
        const ringkasanIsiInput = document.getElementById('edit_ringkasan_isi');
        const keteranganInput = document.getElementById('edit_keterangan');

        const suratKeluar = suratKeluarDataCurrentPage.data.find(surat => surat.id === suratKeluarId);
        const { id, nomor_surat, tanggal_surat, tanggal_keluar, perihal, tujuan, sifat_surat, pengirim_bagian_id, ringkasan_isi, keterangan } = suratKeluar;

        const formatDateForInput = (isoDate) => {
        if (!isoDate) return '';
        return new Date(isoDate).toISOString().split('T')[0];
    };

        idInput.value = id;
        nomorSuratInput.value = nomor_surat || '';
        tanggalSuratInput.value = formatDateForInput(tanggal_surat) || '';
        tanggalKeluarInput.value = formatDateForInput(tanggal_keluar) || '';
        perihalInput.value = perihal || '';
        tujuanInput.value = tujuan || '';
        sifatSuratInput.value = sifat_surat || '';
        pengirimBagianInput.value = pengirim_bagian_id || '';
        ringkasanIsiInput.value = ringkasan_isi || '';
        keteranganInput.value = keterangan || '';

        editSuratKeluarForm.action = `/surat-keluar/${id}`;
    }

    /**
     * ANCHOR: Show Delete Surat Keluar Modal
     * Show the delete surat keluar modal
     * @param {number} suratKeluarId - The id of the surat keluar to delete
     */
    const showDeleteSuratKeluarModal = (suratKeluarId) => {
        const deleteSuratKeluarName = document.getElementById('deleteSuratKeluarName');
        const deleteSuratKeluarForm = document.getElementById('deleteSuratKeluarForm');

        const suratKeluar = suratKeluarDataCurrentPage.data.find(surat => surat.id === suratKeluarId);
        const { id, nomor_surat } = suratKeluar;

        deleteSuratKeluarName.textContent = nomor_surat;
        deleteSuratKeluarForm.action = `/surat-keluar/${id}`;
    }

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
     * ANCHOR: Populate Detail Modal
     * Populate the detail modal with surat keluar data
     * @param {Object} suratKeluar - The surat keluar data
     */
    const populateDetailModal = (suratKeluar) => {
        // Basic information
        document.getElementById('detail-nomor-surat').textContent = suratKeluar.nomor_surat || '-';
        document.getElementById('detail-tanggal-surat').textContent = suratKeluar.tanggal_surat ? 
            new Date(suratKeluar.tanggal_surat).toLocaleDateString('id-ID') : '-';
        document.getElementById('detail-tanggal-keluar').textContent = suratKeluar.tanggal_keluar ? 
            new Date(suratKeluar.tanggal_keluar).toLocaleDateString('id-ID') : '-';
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
        document.getElementById('detail-user').textContent = 
            suratKeluar.user?.username || '-';
        
        // Timestamps
        document.getElementById('detail-created-at').textContent = suratKeluar.created_at ? 
            new Date(suratKeluar.created_at).toLocaleString('id-ID') : '-';

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

    editSuratKeluarHandlers();
    deleteSuratKeluarHandlers();
</script>
@endpush
