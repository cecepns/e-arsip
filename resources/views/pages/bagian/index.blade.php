@extends('layouts.admin')

@push('head')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('admin-content')
<div class="page-header">
    @include('partials.page-title', [
        'title' => 'Manajemen Bagian',
        'subtitle' => 'Kelola unit kerja/divisi di lingkungan instansi.'
    ])
</div>

<div class="mb-3 d-flex justify-content-between align-items-center sub-page-header">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddBagian">
        Tambah Bagian
    </button>
    <form class="d-flex" style="max-width:350px;" method="GET" action="{{ route('bagian.index') }}">
        <div class="input-group">
            <input type="text" name="search" class="form-control" 
                   placeholder="Cari nama bagian atau kepala bagian..." 
                   value="{{ $query ?? '' }}" 
                   autocomplete="off">
            <button class="btn btn-secondary" type="submit" title="Cari">
                <i class="fas fa-search"></i>
            </button>
            @if(isset($query) && $query)
                <a href="{{ route('bagian.index') }}" class="btn btn-danger" title="Hapus pencarian">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </form>
</div>

@if(isset($query) && $query)
    @if($bagian->total() > 0)
    <div class="alert alert-info mb-3">
        <i class="fas fa-search me-2"></i> 
        Hasil pencarian untuk: <strong>"{{ $query }}"</strong> 
        - Ditemukan {{ $bagian->total() }} bagian
    </div>
    @else
    <div class="alert alert-warning mb-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-search me-2"></i>
            <div>
                <strong>Tidak ada hasil ditemukan</strong> untuk pencarian: <strong>"{{ $query }}"</strong>
                <br>
                <small class="text-muted">Coba kata kunci lain atau periksa ejaan</small>
            </div>
        </div>
    </div>
    @endif
@endif

@include('partials.table', [
    'tableId' => 'bagianTable',
    'thead' => view()->make('pages.bagian._table._head')->render(),
    'tbody' => view()->make('pages.bagian._table._body', compact('bagian'))->render(),
])

@include('partials.pagination', [
    'currentPage' => $bagian->currentPage(),
    'totalPages' => $bagian->lastPage(),
    'baseUrl' => route('bagian.index'),
    'showInfo' => "Menampilkan {$bagian->firstItem()}-{$bagian->lastItem()} dari {$bagian->total()} bagian"
])

@include('partials.modal', [
    'id' => 'modalAddBagian',
    'size' => 'modal-md',
    'title' => 'Tambah Bagian',
    'body' => view('pages.bagian._form_modal._add_form', compact('usersNotMarkedAsKepalaBagian'))->render(),
])

@include('partials.modal', [
    'id' => 'modalEditBagian',
    'size' => 'modal-md',
    'title' => 'Edit Bagian',
    'body' => view('pages.bagian._form_modal._edit_form', compact('users'))->render(),
])

@include('partials.modal', [
    'id' => 'modalBagianDetail',
    'title' => 'Detail Bagian',
    'size' => 'modal-xl',
    'body' => view()->make('pages.bagian._detail_modal._body')->render(),
])

@include('partials.modal', [
    'id' => 'modalDetailSuratMasuk',
    'size' => 'modal-xl',
    'title' => 'Detail Surat Masuk',
    'body' => view()->make('pages.surat_masuk._detail_modal._body')->render(),
    'footer' => view()->make('pages.surat_masuk._detail_modal._footer')->render(),
])

@include('partials.modal', [
    'id' => 'modalDetailSuratKeluar',
    'size' => 'modal-xl',
    'title' => 'Detail Surat Keluar',
    'body' => view()->make('pages.surat_keluar._detail_modal._body')->render(),
    'footer' => view()->make('pages.surat_keluar._detail_modal._footer')->render(),
])

@include('partials.modal', [
    'type' => 'danger',
    'id' => 'modalDeleteBagian',
    'title' => 'Konfirmasi Hapus Bagian',
    'size' => 'modal-md',
    'body' => view()->make('pages.bagian._delete_modal._body')->render(),
])
@endsection

@push('scripts')
<script>
    // ANCHOR: Make data globally accessible for modals
    window.bagianDataCurrentPage = {!! json_encode($bagian->items()) !!};
    window.usersData = {!! json_encode($users) !!};

    const SURAT_TYPES = Object.freeze({
        MASUK: 'masuk',
        KELUAR: 'keluar'
    });

    let reopenBagianModalOnDetailClose = false;

    /**
     * ANCHOR: Handle Surat Modal Hidden
     * Reopen bagian detail modal when surat detail modal is closed
     */
    function handleSuratModalHidden() {
        if (!reopenBagianModalOnDetailClose) {
            return;
        }

        const bagianModalEl = document.getElementById('modalBagianDetail');
        if (!bagianModalEl) {
            reopenBagianModalOnDetailClose = false;
            return;
        }

        const bagianModalInstance = bootstrap.Modal.getOrCreateInstance(bagianModalEl);
        bagianModalInstance.show();
        reopenBagianModalOnDetailClose = false;
    }

    const modalDetailSuratMasukEl = document.getElementById('modalDetailSuratMasuk');
    if (modalDetailSuratMasukEl) {
        modalDetailSuratMasukEl.addEventListener('hidden.bs.modal', handleSuratModalHidden);
    }

    const modalDetailSuratKeluarEl = document.getElementById('modalDetailSuratKeluar');
    if (modalDetailSuratKeluarEl) {
        modalDetailSuratKeluarEl.addEventListener('hidden.bs.modal', handleSuratModalHidden);
    }

    /**
     * ANCHOR: Format Date
     * Format date strings to Indonesian locale (DD/MM/YYYY)
     * @param {string|null|undefined} value - The date value to format
     * @returns {string}
     */
    function formatDate(value) {
        return value ? new Date(value).toLocaleDateString('id-ID') : '-';
    }

    /**
     * ANCHOR: Format DateTime
     * Format datetime strings to Indonesian locale with time
     * @param {string|null|undefined} value - The datetime value to format
     * @returns {string}
     */
    function formatDateTime(value) {
        return value ? new Date(value).toLocaleString('id-ID') : '-';
    }

    /**
     * ANCHOR: Set Element Text
     * Set text content for an element inside a parent element
     * @param {HTMLElement} parentEl - Parent element to query
     * @param {string} selector - Selector inside the parent element
     * @param {string} value - Text value to assign
     */
    function setElementText(parentEl, selector, value) {
        if (!parentEl) return;
        const element = parentEl.querySelector(selector);
        if (element) {
            element.textContent = value ?? '-';
        }
    }

    /**
     * ANCHOR: Toggle Section Display
     * Show or hide a section element
     * @param {HTMLElement|null} sectionEl - Section element
     * @param {boolean} shouldShow - Whether to show the section
     */
    function toggleSectionDisplay(sectionEl, shouldShow) {
        if (!sectionEl) return;
        sectionEl.style.display = shouldShow ? 'block' : 'none';
    }

    /**
     * ANCHOR: Reset Surat Modal
     * Reset modal content to loading state before data is populated
     * @param {HTMLElement|null} parentEl - Modal element to reset
     */
    function resetSuratModal(parentEl) {
        if (!parentEl) return;

        const textSelectors = [
            '#detail-nomor-surat',
            '#detail-tanggal-surat',
            '#detail-tanggal-terima',
            '#detail-tanggal-keluar',
            '#detail-perihal',
            '#detail-pengirim',
            '#detail-sifat-surat',
            '#detail-bagian-tujuan',
            '#detail-bagian-pengirim',
            '#detail-user',
            '#detail-created-at',
            '#detail-updated-by',
            '#detail-updated-at',
            '#detail-tujuan'
        ];

        textSelectors.forEach((selector) => setElementText(parentEl, selector, '-'));

        const ringkasanSection = parentEl.querySelector('#detail-ringkasan-section');
        toggleSectionDisplay(ringkasanSection, false);

        const keteranganSection = parentEl.querySelector('#detail-keterangan-section');
        toggleSectionDisplay(keteranganSection, false);

        const lampiranContent = parentEl.querySelector('#detail-lampiran-content');
        if (lampiranContent) {
            lampiranContent.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <p>Memuat lampiran...</p>
                </div>
            `;
        }

        const disposisiSection = parentEl.querySelector('#detail-disposisi-section');
        const disposisiContent = parentEl.querySelector('#detail-disposisi-content');
        if (disposisiSection) {
            disposisiSection.style.display = 'none';
        }
        if (disposisiContent) {
            disposisiContent.innerHTML = '';
        }
    }

    /**
     * ANCHOR: Show Surat Detail Error
     * Render error state within the lampiran container
     * @param {HTMLElement|null} parentEl - Modal element
     * @param {string} message - Error message
     */
    function showSuratDetailError(parentEl, message) {
        const lampiranContent = parentEl?.querySelector('#detail-lampiran-content');
        if (!lampiranContent) return;

        lampiranContent.innerHTML = `
            <div class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <p class="mb-0">${message || 'Terjadi kesalahan saat memuat data.'}</p>
            </div>
        `;
    }

    /**
     * ANCHOR: Populate Lampiran Detail
     * Populate lampiran section with attachment data
     * @param {Array} lampiran - Lampiran data
     * @param {HTMLElement} parentEl - Modal element
     */
    function populateLampiranDetail(lampiran, parentEl) {
        const lampiranContent = parentEl.querySelector('#detail-lampiran-content');
        if (!lampiranContent) return;

        if (!lampiran || lampiran.length === 0) {
            lampiranContent.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-paperclip fa-2x mb-2"></i>
                    <p class="mb-0">Tidak ada lampiran</p>
                </div>
            `;
            return;
        }

        const itemsHtml = lampiran.map((file) => {
            const fileName = file.nama_file || 'Lampiran';
            const fileTypeLabel = file.tipe_lampiran === 'utama' ? 'Lampiran Utama' : 'Dokumen Pendukung';
            const downloadUrl = file.path_file ? `/storage/${file.path_file}` : '#';

            return `
                <div class="card border mb-3">
                    <div class="card-body p-3 d-flex justify-content-between align-items-start gap-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-truncate" title="${fileName}">${fileName}</h6>
                            <small class="text-muted">${fileTypeLabel}</small>
                        </div>
                        <div>
                            <a href="${downloadUrl}" class="btn btn-sm btn-outline-primary"
                               title="Download ${fileName}" ${downloadUrl !== '#' ? `download="${fileName}"` : 'aria-disabled="true"'}>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        lampiranContent.innerHTML = `<div>${itemsHtml}</div>`;
    }

    /**
     * ANCHOR: Populate Disposisi Detail
     * Populate disposisi section for surat masuk detail
     * @param {Array} disposisi - Disposisi data
     * @param {HTMLElement} parentEl - Modal element
     * @param {Object} suratMasuk - Surat masuk data reference
     */
    function populateDisposisiDetail(disposisi, parentEl, suratMasuk) {
        const disposisiSection = parentEl.querySelector('#detail-disposisi-section');
        const disposisiContent = parentEl.querySelector('#detail-disposisi-content');

        if (!disposisiSection || !disposisiContent) return;

        if (!disposisi || disposisi.length === 0) {
            disposisiSection.style.display = 'none';
            disposisiContent.innerHTML = '';
            return;
        }

        const kepalaBagianPengirim = suratMasuk?.tujuan_bagian?.kepala_bagian?.nama || '-';
        const namaBagianPengirim = suratMasuk?.tujuan_bagian?.nama_bagian || '-';

        const disposisiHtml = disposisi.map((disp, index) => {
            const statusBadgeClass =
                disp.status === 'Menunggu' ? 'bg-warning' :
                disp.status === 'Dikerjakan' ? 'bg-info' :
                disp.status === 'Selesai' ? 'bg-success' : 'bg-secondary';

            const kepalaBagianTujuan = disp.tujuan_bagian?.kepala_bagian?.nama || '-';
            const namaBagianTujuan = disp.tujuan_bagian?.nama_bagian || '-';

            return `
                <div class="card border mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-primary">
                            <i class="fas fa-share-alt me-2"></i>Disposisi ${index + 1}
                        </h6>
                        <span class="badge ${statusBadgeClass}">${disp.status || 'Tidak diketahui'}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <span class="fw-semibold text-dark">Dibuat Oleh:</span>
                                    <span class="text-secondary">${disp.user?.nama || '-'}</span>
                                </p>
                                <p class="mb-2">
                                    <span class="fw-semibold text-dark">Disposisi Dari:</span>
                                    <span class="text-secondary">${kepalaBagianPengirim} (${namaBagianPengirim})</span>
                                </p>
                                <p class="mb-2">
                                    <span class="fw-semibold text-dark">Dibuat Pada:</span>
                                    <span class="text-muted">${formatDateTime(disp.created_at)}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <span class="fw-semibold text-dark">Disposisi Kepada:</span>
                                    <span class="text-secondary">${kepalaBagianTujuan} (${namaBagianTujuan})</span>
                                </p>
                                <p class="mb-2">
                                    <span class="fw-semibold text-dark">Instruksi:</span>
                                    <span class="text-dark">${disp.isi_instruksi || '-'}</span>
                                </p>
                                <p class="mb-2">
                                    <span class="fw-semibold text-dark">Tanggal Disposisi:</span>
                                    <span class="text-secondary">${formatDate(disp.tanggal_disposisi)}</span>
                                </p>
                                <p class="mb-0">
                                    <span class="fw-semibold text-dark">Batas Waktu:</span>
                                    <span class="text-secondary">${formatDate(disp.batas_waktu)}</span>
                                </p>
                            </div>
                        </div>
                        ${disp.catatan ? `
                            <div class="mt-3 pt-3 border-top">
                                <p class="mb-2 fw-semibold text-dark">Catatan:</p>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0 text-dark">${disp.catatan}</p>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');

        disposisiContent.innerHTML = disposisiHtml;
        disposisiSection.style.display = 'block';
    }

    /**
     * ANCHOR: Populate Surat Masuk Modal
     * Populate surat masuk modal with fetched data
     * @param {Object} data - API response
     */
    function populateSuratMasukDetailFromBagian(data) {
        const parentEl = document.getElementById('modalDetailSuratMasuk');
        if (!parentEl) return;

        const suratMasuk = data?.suratMasuk || data;
        if (!suratMasuk) return;

        window.currentDetailSuratMasukId = suratMasuk.id;
        window.currentDetailSuratMasuk = suratMasuk;

        setElementText(parentEl, '#detail-nomor-surat', suratMasuk.nomor_surat || '-');
        setElementText(parentEl, '#detail-tanggal-surat', formatDate(suratMasuk.tanggal_surat));
        setElementText(parentEl, '#detail-tanggal-terima', formatDate(suratMasuk.tanggal_terima));
        setElementText(parentEl, '#detail-perihal', suratMasuk.perihal || '-');
        setElementText(parentEl, '#detail-pengirim', suratMasuk.pengirim || '-');
        setElementText(parentEl, '#detail-sifat-surat', suratMasuk.sifat_surat || '-');
        setElementText(parentEl, '#detail-bagian-tujuan', suratMasuk.tujuan_bagian?.nama_bagian || '-');
        setElementText(parentEl, '#detail-user', suratMasuk.user?.nama || '-');
        setElementText(parentEl, '#detail-created-at', formatDateTime(suratMasuk.created_at));
        setElementText(parentEl, '#detail-updated-by', suratMasuk.updater?.nama || '-');
        setElementText(parentEl, '#detail-updated-at', formatDateTime(suratMasuk.updated_at));

        const ringkasanSection = parentEl.querySelector('#detail-ringkasan-section');
        const ringkasanContent = parentEl.querySelector('#detail-ringkasan-isi');
        toggleSectionDisplay(ringkasanSection, Boolean(suratMasuk.ringkasan_isi));
        if (ringkasanContent) {
            ringkasanContent.textContent = suratMasuk.ringkasan_isi || '-';
        }

        const keteranganSection = parentEl.querySelector('#detail-keterangan-section');
        const keteranganContent = parentEl.querySelector('#detail-keterangan');
        toggleSectionDisplay(keteranganSection, Boolean(suratMasuk.keterangan));
        if (keteranganContent) {
            keteranganContent.textContent = suratMasuk.keterangan || '-';
        }

        populateLampiranDetail(suratMasuk.lampiran || [], parentEl);
        populateDisposisiDetail(suratMasuk.disposisi || [], parentEl, suratMasuk);
    }

    /**
     * ANCHOR: Populate Surat Keluar Modal
     * Populate surat keluar modal with fetched data
     * @param {Object} data - API response
     */
    function populateSuratKeluarDetailFromBagian(data) {
        const parentEl = document.getElementById('modalDetailSuratKeluar');
        if (!parentEl) return;

        const suratKeluar = data?.suratKeluar || data;
        if (!suratKeluar) return;

        window.currentDetailSuratKeluarId = suratKeluar.id;
        window.currentDetailSuratKeluar = suratKeluar;

        setElementText(parentEl, '#detail-nomor-surat', suratKeluar.nomor_surat || '-');
        setElementText(parentEl, '#detail-tanggal-surat', formatDate(suratKeluar.tanggal_surat));
        setElementText(parentEl, '#detail-tanggal-keluar', formatDate(suratKeluar.tanggal_keluar));
        setElementText(parentEl, '#detail-perihal', suratKeluar.perihal || '-');
        setElementText(parentEl, '#detail-sifat-surat', suratKeluar.sifat_surat || '-');
        setElementText(parentEl, '#detail-tujuan', suratKeluar.tujuan || '-');
        setElementText(parentEl, '#detail-bagian-pengirim', suratKeluar.pengirim_bagian?.nama_bagian || '-');
        setElementText(parentEl, '#detail-user', suratKeluar.user?.username || suratKeluar.user?.nama || '-');
        setElementText(parentEl, '#detail-created-at', formatDateTime(suratKeluar.created_at));
        setElementText(parentEl, '#detail-updated-by', suratKeluar.updater?.username || suratKeluar.updater?.nama || '-');
        setElementText(parentEl, '#detail-updated-at', formatDateTime(suratKeluar.updated_at));

        const ringkasanSection = parentEl.querySelector('#detail-ringkasan-section');
        const ringkasanContent = parentEl.querySelector('#detail-ringkasan-isi');
        toggleSectionDisplay(ringkasanSection, Boolean(suratKeluar.ringkasan_isi));
        if (ringkasanContent) {
            ringkasanContent.textContent = suratKeluar.ringkasan_isi || '-';
        }

        const keteranganSection = parentEl.querySelector('#detail-keterangan-section');
        const keteranganContent = parentEl.querySelector('#detail-keterangan');
        toggleSectionDisplay(keteranganSection, Boolean(suratKeluar.keterangan));
        if (keteranganContent) {
            keteranganContent.textContent = suratKeluar.keterangan || '-';
        }

        populateLampiranDetail(suratKeluar.lampiran || [], parentEl);

        const disposisiSection = parentEl.querySelector('#detail-disposisi-section');
        if (disposisiSection) {
            disposisiSection.style.display = 'none';
        }
    }

    /**
     * ANCHOR: Prepare Surat Detail Modal
     * Hide bagian detail modal and set flag to reopen after surat modal closes
     */
    function prepareForSuratDetailModal() {
        const bagianModalEl = document.getElementById('modalBagianDetail');
        if (!bagianModalEl) {
            reopenBagianModalOnDetailClose = false;
            return;
        }

        const isBagianVisible = bagianModalEl.classList.contains('show');
        const bagianModalInstance = bootstrap.Modal.getOrCreateInstance(bagianModalEl);

        if (isBagianVisible) {
            reopenBagianModalOnDetailClose = true;
            bagianModalInstance.hide();
        } else {
            reopenBagianModalOnDetailClose = false;
        }
    }

    /**
     * ANCHOR: Load Surat Masuk Detail
     * Fetch and display surat masuk detail in modal
     * @param {number} suratId - Surat masuk ID
     */
    async function loadSuratMasukDetail(suratId) {
        prepareForSuratDetailModal();

        const modalEl = document.getElementById('modalDetailSuratMasuk');
        if (!modalEl) return;

        resetSuratModal(modalEl);
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();

        try {
            const response = await fetch(`/surat-masuk/${suratId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok || data.success === false) {
                throw new Error(data.message || 'Gagal memuat detail surat masuk.');
            }

            populateSuratMasukDetailFromBagian(data);

        } catch (error) {
            console.error('Error loading surat masuk detail:', error);
            showSuratDetailError(modalEl, error.message);
        }
    }

    /**
     * ANCHOR: Load Surat Keluar Detail
     * Fetch and display surat keluar detail in modal
     * @param {number} suratId - Surat keluar ID
     */
    async function loadSuratKeluarDetail(suratId) {
        prepareForSuratDetailModal();

        const modalEl = document.getElementById('modalDetailSuratKeluar');
        if (!modalEl) return;

        resetSuratModal(modalEl);
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();

        try {
            const response = await fetch(`/surat-keluar/${suratId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok || data.success === false) {
                throw new Error(data.message || 'Gagal memuat detail surat keluar.');
            }

            populateSuratKeluarDetailFromBagian(data);

        } catch (error) {
            console.error('Error loading surat keluar detail:', error);
            showSuratDetailError(modalEl, error.message);
        }
    }

    /**
     * ANCHOR: Show Detail Bagian Modal
     * Show the detail bagian modal and populate with cached data
     * @param {number} bagianId - The id of the bagian to show the details of
     */
    const showDetailBagianModal = async (bagianId) => {
        try {
            // Show loading state
            const modalBagianDetail = document.getElementById('modalBagianDetail');
            const modalBody = modalBagianDetail.querySelector('.modal-body');
            modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

            // Find bagian data from cached data first
            const bagian = window.bagianDataCurrentPage.find(item => item.id == bagianId);
            
            if (bagian) {
                // Use cached data - no need for XHR request
                populateDetailModal(bagian);
            } else {
                // Fallback: Fetch from server if not found in current page
                const response = await fetch(`/bagian/${bagianId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch bagian details');
                }

                const data = await response.json();
                populateDetailModal(data.bagian);
            }

        } catch (error) {
            console.error('Error fetching bagian details:', error);
            const modalBody = document.getElementById('modalBagianDetail').querySelector('.modal-body');
            modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat data bagian. Silakan coba lagi.</div>';
        }
    }

    /**
     * ANCHOR: Populate Detail Modal
     * Populate the detail modal with bagian data
     * @param {Object} bagian - The bagian data to display
     */
    const populateDetailModal = (bagian) => {
        const modalBody = document.getElementById('modalBagianDetail').querySelector('.modal-body');
        
        // Calculate statistics
        const staffCount = bagian.users ? bagian.users.length : 0;
        const suratMasukCount = bagian.surat_masuk ? bagian.surat_masuk.length : 0;
        const suratKeluarCount = bagian.surat_keluar ? bagian.surat_keluar.length : 0;
        const totalSurat = suratMasukCount + suratKeluarCount;

        // Get kepala bagian name
        const kepalaBagianName = bagian.kepala_bagian ? 
            (bagian.kepala_bagian.nama || bagian.kepala_bagian.username) : 
            'Belum ditentukan';

        modalBody.innerHTML = `
            {{-- SECTION: Informasi Bagian --}}
            <div class="mb-3">
                <h5 class="mb-3">Informasi Bagian</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="mb-2"><span class="fw-semibold">Nama Bagian:</span> ${bagian.nama_bagian}</p>
                            <p class="mb-2"><span class="fw-semibold">Kepala Bagian:</span> ${kepalaBagianName}</p>
                            <p class="mb-2"><span class="fw-semibold">Jumlah Staff:</span> ${staffCount}</p>
                            <p class="mb-2"><span class="fw-semibold">Status:</span> 
                                <span class="badge ${bagian.status === 'Aktif' ? 'bg-success' : 'bg-secondary'}">${bagian.status}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p><span class="fw-semibold">Deskripsi:</span><br> ${bagian.keterangan || 'Tidak ada deskripsi'}</p>
                        </div>
                        <div class="mb-3">
                            <p><span class="fw-semibold">Total Surat:</span> ${totalSurat}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- !SECTION: Informasi Bagian --}}
            <div class="mb-3">
                <h5 class="mb-3">Surat Masuk/Keluar</h5>
                <div id="suratTableContainer">
                    <div class="text-center">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data surat...</p>
                    </div>
                </div>
            </div>
        `;

        // Load surat data
        loadSuratData(bagian.id);
    }

    /**
     * ANCHOR: Load Surat Data
     * Load and display surat data for the bagian
     * @param {number} bagianId - The id of the bagian
     */
    const loadSuratData = async (bagianId) => {
        try {
            const response = await fetch(`/bagian/${bagianId}/surat`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch surat data');
            }

            const data = await response.json();
            const suratData = data.surat || [];

            // Populate surat table
            populateSuratTable(suratData);

        } catch (error) {
            console.error('Error fetching surat data:', error);
            document.getElementById('suratTableContainer').innerHTML = 
                '<div class="alert alert-warning">Gagal memuat data surat.</div>';
        }
    }

    /**
     * ANCHOR: Populate Surat Table
     * Populate the surat table with data
     * @param {Array} suratData - Array of surat data
     */
    const populateSuratTable = (suratData) => {
        const container = document.getElementById('suratTableContainer');
        
        if (suratData.length === 0) {
            container.innerHTML = '<div class="alert alert-info">Belum ada surat untuk bagian ini.</div>';
            return;
        }

        let tableHTML = `
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Surat</th>
                            <th>Tanggal Surat</th>
                            <th>Perihal</th>
                            <th>Jenis Surat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        suratData.forEach((surat, index) => {
            const jenisSuratLabel = surat.jenis === 'masuk' ? 'Surat Masuk' : 'Surat Keluar';
            const badgeClass = surat.jenis === 'masuk' ? 'badge-incoming' : 'badge-outgoing';
            
            tableHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${surat.nomor_surat}</strong></td>
                    <td>${new Date(surat.tanggal_surat).toLocaleDateString('id-ID')}</td>
                    <td>${surat.perihal}</td>
                    <td><span class="${badgeClass}">${jenisSuratLabel}</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat" onclick="viewSuratDetail(${surat.id}, '${surat.jenis}')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableHTML += `
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = tableHTML;
    }

    /**
     * ANCHOR: View Surat Detail
     * View detail of a specific surat
     * @param {number} suratId - The id of the surat
     * @param {string} jenisSurat - The type of surat (masuk/keluar)
     */
    window.viewSuratDetail = async (suratId, jenisSurat) => {
        const normalizedType = (jenisSurat || '').toString().toLowerCase();

        if (normalizedType === SURAT_TYPES.MASUK) {
            await loadSuratMasukDetail(suratId);
            return;
        }

        if (normalizedType === SURAT_TYPES.KELUAR) {
            await loadSuratKeluarDetail(suratId);
            return;
        }

        console.warn(`Jenis surat tidak dikenali: ${jenisSurat}`);
    }


</script>
@endpush
