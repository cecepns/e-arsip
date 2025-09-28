@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    @include('partials.page-title', [
        'title' => 'Manajemen Disposisi',
        'subtitle' => 'Kelola disposisi surat masuk beserta status dan tindak lanjutnya.'
    ])
</div>

<div class="mb-3 d-flex justify-content-end align-items-center">
    <div class="d-flex gap-2">
         <button class="btn btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
             Filter Lanjutan
         </button>
        <form class="d-flex" style="max-width:300px;" method="GET" action="{{ route('disposisi.index') }}">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari nomor/perihal..." value="{{ $filters['query'] ?? '' }}">
             <button class="btn btn-outline-secondary" type="submit">Cari</button>
            @if(isset($filters['query']) && $filters['query'])
                 <a href="{{ route('disposisi.index') }}" class="btn btn-outline-danger ms-1" title="Clear search">
                     ×
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
                 Filter Lanjutan
             </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('disposisi.index') }}" id="filterForm" class="filter-form">
                <div class="row g-3">
                    <!-- Status Disposisi -->
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status Disposisi</label>
                        <select name="status" class="form-select" id="status">
                            <option value="">Semua Status</option>
                            <option value="Menunggu" {{ ($filters['status'] ?? '') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="Dikerjakan" {{ ($filters['status'] ?? '') == 'Dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                            <option value="Selesai" {{ ($filters['status'] ?? '') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    
                    <!-- Bagian Tujuan -->
                    <div class="col-md-3">
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
                    
                    <!-- Sifat Surat -->
                    <div class="col-md-3">
                        <label for="sifat_surat" class="form-label">Sifat Surat</label>
                        <select name="sifat_surat" class="form-select" id="sifat_surat">
                            <option value="">Semua Sifat</option>
                            <option value="Biasa" {{ ($filters['sifat_surat'] ?? '') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                            <option value="Segera" {{ ($filters['sifat_surat'] ?? '') == 'Segera' ? 'selected' : '' }}>Segera</option>
                            <option value="Penting" {{ ($filters['sifat_surat'] ?? '') == 'Penting' ? 'selected' : '' }}>Penting</option>
                            <option value="Rahasia" {{ ($filters['sifat_surat'] ?? '') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                        </select>
                    </div>
                    
                    <!-- Tanggal Disposisi -->
                    <div class="col-md-3">
                        <label for="tanggal" class="form-label">Tanggal Disposisi</label>
                        <input type="date" name="tanggal" class="form-control" id="tanggal" value="{{ $filters['tanggal'] ?? '' }}">
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="col-12 d-flex gap-2">
                         <button type="submit" class="btn btn-primary" id="applyFilterBtn">
                             Terapkan Filter
                         </button>
                         <a href="{{ route('disposisi.index') }}" class="btn btn-outline-secondary">
                             Reset Filter
                         </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($filters['status']) && $filters['status'] || isset($filters['bagian_id']) && $filters['bagian_id'] || isset($filters['tanggal']) && $filters['tanggal'] || isset($filters['sifat_surat']) && $filters['sifat_surat'])
 <div class="alert alert-info mb-3">
     <strong>Filter Aktif:</strong>
    @if(isset($filters['status']) && $filters['status'])
        <span class="badge bg-warning me-1">Status: {{ $filters['status'] }}</span>
    @endif
    @if(isset($filters['bagian_id']) && $filters['bagian_id'])
        @php
            $selectedBagian = $bagian->where('id', $filters['bagian_id'])->first();
        @endphp
        <span class="badge bg-info me-1">Bagian: {{ $selectedBagian->nama_bagian ?? 'Unknown' }}</span>
    @endif
    @if(isset($filters['sifat_surat']) && $filters['sifat_surat'])
        <span class="badge bg-secondary me-1">Sifat: {{ $filters['sifat_surat'] }}</span>
    @endif
    @if(isset($filters['tanggal']) && $filters['tanggal'])
        <span class="badge bg-success me-1">Tanggal: {{ \Carbon\Carbon::parse($filters['tanggal'])->format('d/m/Y') }}</span>
    @endif
    <span class="ms-2">Ditemukan {{ $disposisi->total() }} disposisi</span>
</div>
@endif

@include('partials.table', [
    'tableId' => 'disposisiTable',
    'thead' => view()->make('pages.disposisi._table._head')->render(),
    'tbody' => view()->make('pages.disposisi._table._body', compact('disposisi'))->render(),
])

@include('partials.pagination', [
    'currentPage' => $disposisi->currentPage(),
    'totalPages' => $disposisi->lastPage(),
    'baseUrl' => route('disposisi.index'),
    'showInfo' => 'Menampilkan ' . $disposisi->firstItem() . '-' . $disposisi->lastItem() . ' dari ' . $disposisi->total() . ' disposisi'
])

@include('partials.modal', [
    'id' => 'modalDetailDisposisi',
    'size' => 'modal-lg',
    'title' => 'Detail Disposisi',
    'body' => view()->make('pages.disposisi._detail_modal._body')->render(),
    'footer' => view()->make('pages.disposisi._detail_modal._footer')->render(),
])

@include('partials.modal', [
    'id' => 'modalEditDisposisi',
    'size' => 'modal-lg',
    'title' => 'Edit Disposisi',
    'body' => view('pages.disposisi._form_modal.edit_form', compact('bagian'))->render(),
    'footer' => view('pages.disposisi._form_modal.edit_footer')->render(),
])

@include('partials.modal', [
    'type' => 'danger',
    'id' => 'modalDeleteDisposisi',
    'title' => 'Konfirmasi Hapus Disposisi',
    'size' => 'modal-md',
    'body' => view()->make('pages.disposisi._delete_modal._body')->render(),
])

@endsection

@push('scripts')
<script src="{{ asset('js/utils.js') }}"></script>
<script>
    const disposisiDataCurrentPage = {!! json_encode($disposisi) !!};

document.addEventListener('DOMContentLoaded', function() {
    // ANCHOR: Handle filter form submission
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }
            
            window.location.href = '{{ route("disposisi.index") }}?' + params.toString();
        });
    }

    // ANCHOR: Auto-submit filter on change
    const filterSelects = document.querySelectorAll('#filterForm select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    /**
     * ANCHOR: Show Detail Disposisi Modal
     * Show the detail disposisi modal
     * @param {number} disposisiId - The id of the disposisi to show
     */
    window.showDisposisiDetail = async (disposisiId) => {
        try {
            // Fetch detail data
            const csrfToken = (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                document.querySelector('input[name="_token"]')?.value
            );

            const response = await fetch(`/disposisi/${disposisiId}`, {
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
                populateDetailModal(data.disposisi);
                const modal = new bootstrap.Modal(document.getElementById('modalDetailDisposisi'));
                modal.show();
            } else {
                throw new Error(data.message || 'Failed to load detail');
            }

        } catch (error) {
            console.error('Error loading detail:', error);
            showToast('Gagal memuat detail disposisi', 'error', 3000);
        }
    };

    /**
     * ANCHOR: Show Edit Disposisi Modal
     * Show the edit disposisi modal
     * @param {number} disposisiId - The id of the disposisi to edit
     */
    window.editDisposisi = async (disposisiId) => {
        try {
            // Fetch detail data
            const csrfToken = (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                document.querySelector('input[name="_token"]')?.value
            );

            const response = await fetch(`/disposisi/${disposisiId}`, {
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
                populateEditForm(data.disposisi);
                const modal = new bootstrap.Modal(document.getElementById('modalEditDisposisi'));
                modal.show();
            } else {
                throw new Error(data.message || 'Failed to load detail');
            }

        } catch (error) {
            console.error('Error loading detail:', error);
            showToast('Gagal memuat data disposisi', 'error', 3000);
        }
    };

    /**
     * ANCHOR: Show Delete Disposisi Modal
     * Show the delete disposisi modal
     * @param {number} disposisiId - The id of the disposisi to delete
     * @param {string} nomorSurat - The nomor surat for display
     */
    window.deleteDisposisi = (disposisiId, nomorSurat) => {
        document.getElementById('deleteDisposisiId').value = disposisiId;
        document.getElementById('deleteDisposisiNomor').textContent = nomorSurat;
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteDisposisi'));
        modal.show();
    };

    // ANCHOR: Populate detail modal
    function populateDetailModal(disposisi) {
        document.getElementById('detailNomorSurat').textContent = disposisi.surat_masuk?.nomor_surat || '-';
        document.getElementById('detailTanggalSurat').textContent = disposisi.surat_masuk?.tanggal_surat ? 
            new Date(disposisi.surat_masuk.tanggal_surat).toLocaleDateString('id-ID') : '-';
        document.getElementById('detailPerihal').textContent = disposisi.surat_masuk?.perihal || '-';
        document.getElementById('detailPengirim').textContent = disposisi.surat_masuk?.pengirim || '-';
        document.getElementById('detailDisposisiDari').textContent = disposisi.surat_masuk?.tujuan_bagian?.kepala_bagian?.nama || 'Belum ditentukan';
        document.getElementById('detailDisposisiDariBagian').textContent = disposisi.surat_masuk?.tujuan_bagian?.nama_bagian || '-';
        document.getElementById('detailDisposisiKepada').textContent = disposisi.tujuan_bagian?.kepala_bagian?.nama || 'Belum ditentukan';
        document.getElementById('detailDisposisiKepadaBagian').textContent = disposisi.tujuan_bagian?.nama_bagian || '-';
        document.getElementById('detailSifatSurat').textContent = disposisi.surat_masuk?.sifat_surat || '-';
        document.getElementById('detailTanggalDisposisi').textContent = disposisi.tanggal_disposisi ? 
            new Date(disposisi.tanggal_disposisi).toLocaleDateString('id-ID') : '-';
        document.getElementById('detailBatasWaktu').textContent = disposisi.batas_waktu ? 
            new Date(disposisi.batas_waktu).toLocaleDateString('id-ID') : '-';
        document.getElementById('detailInstruksi').textContent = disposisi.isi_instruksi || '-';
        document.getElementById('detailCatatan').textContent = disposisi.catatan || '-';
        document.getElementById('detailStatus').textContent = disposisi.status || '-';
        document.getElementById('detailDibuatOleh').textContent = disposisi.user?.nama || '-';
        document.getElementById('detailDibuatTanggal').textContent = disposisi.created_at ? 
            new Date(disposisi.created_at).toLocaleDateString('id-ID') : '-';
    }

    // ANCHOR: Populate edit form
    function populateEditForm(disposisi) {
        document.getElementById('editDisposisiId').value = disposisi.id;
        document.getElementById('editTujuanBagian').value = disposisi.tujuan_bagian_id || '';
        document.getElementById('editStatus').value = disposisi.status || '';
        document.getElementById('editInstruksi').value = disposisi.isi_instruksi || '';
        document.getElementById('editCatatan').value = disposisi.catatan || '';
        
        // Format tanggal untuk input date (YYYY-MM-DD)
        document.getElementById('editTanggalDisposisi').value = disposisi.tanggal_disposisi ? 
            new Date(disposisi.tanggal_disposisi).toISOString().split('T')[0] : '';
        document.getElementById('editBatasWaktu').value = disposisi.batas_waktu ? 
            new Date(disposisi.batas_waktu).toISOString().split('T')[0] : '';
        
        // Populate surat masuk info (read-only)
        if (disposisi.surat_masuk) {
            document.getElementById('editNomorSurat').value = disposisi.surat_masuk.nomor_surat || '';
            document.getElementById('editPerihal').value = disposisi.surat_masuk.perihal || '';
            document.getElementById('editPengirim').value = disposisi.surat_masuk.pengirim || '';
            document.getElementById('editSifatSurat').value = disposisi.surat_masuk.sifat_surat || '';
            
            // Populate disposisi dari info
            document.getElementById('editDisposisiDari').textContent = disposisi.surat_masuk.tujuan_bagian?.kepala_bagian?.nama || 'Belum ditentukan';
            document.getElementById('editDisposisiDariBagian').textContent = disposisi.surat_masuk.tujuan_bagian?.nama_bagian || '-';
        }
    }

    // ANCHOR: Handle edit form submission
    const editForm = document.getElementById('editDisposisiForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const disposisiId = formData.get('disposisi_id');
            
            fetch(`/disposisi/${disposisiId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    tujuan_bagian_id: formData.get('tujuan_bagian_id'),
                    status: formData.get('status'),
                    isi_instruksi: formData.get('isi_instruksi'),
                    catatan: formData.get('catatan'),
                    tanggal_disposisi: formData.get('tanggal_disposisi'),
                    batas_waktu: formData.get('batas_waktu'),
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Disposisi berhasil diperbarui', 'success', 3000);
                    
                    // Close modal and reload page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditDisposisi'));
                    modal.hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(data.message || 'Gagal memperbarui disposisi', 'error', 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Terjadi kesalahan saat memperbarui disposisi', 'error', 3000);
            });
        });
    }

    // ANCHOR: Handle delete form submission
    const deleteForm = document.getElementById('deleteDisposisiForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const disposisiId = document.getElementById('deleteDisposisiId').value;
            
            fetch(`/disposisi/${disposisiId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Disposisi berhasil dihapus', 'success', 3000);
                    
                    // Close modal and reload page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalDeleteDisposisi'));
                    modal.hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(data.message || 'Gagal menghapus disposisi', 'error', 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menghapus disposisi', 'error', 3000);
            });
        });
    }
});
</script>
@endpush

