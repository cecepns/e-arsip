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

<div class="mb-3 d-flex justify-content-between align-items-center">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddBagian">
        Tambah Bagian
    </button>
    <form class="d-flex" style="max-width:350px;" method="GET" action="{{ route('bagian.index') }}">
        <div class="input-group">
            <input type="text" name="search" class="form-control" 
                   placeholder="Cari nama bagian atau kepala bagian..." 
                   value="{{ $query ?? '' }}" 
                   autocomplete="off">
            <button class="btn btn-outline-secondary" type="submit" title="Cari">
                <i class="fas fa-search"></i>
            </button>
            @if(isset($query) && $query)
                <a href="{{ route('bagian.index') }}" class="btn btn-outline-danger" title="Hapus pencarian">
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
            const jenisSurat = surat.jenis === 'masuk' ? 'Surat Masuk' : 'Surat Keluar';
            const badgeClass = surat.jenis === 'masuk' ? 'badge-incoming' : 'badge-outgoing';
            
            tableHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${surat.nomor_surat}</strong></td>
                    <td>${new Date(surat.tanggal_surat).toLocaleDateString('id-ID')}</td>
                    <td>${surat.perihal}</td>
                    <td><span class="${badgeClass}">${jenisSurat}</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat" onclick="viewSuratDetail(${surat.id}, '${jenisSurat}')">
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
    const viewSuratDetail = (suratId, jenisSurat) => {
        // For now, just show an alert. This can be enhanced later
        alert(`Viewing ${jenisSurat} with ID: ${suratId}`);
    }


</script>
@endpush
