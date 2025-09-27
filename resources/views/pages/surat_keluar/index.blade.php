@extends('layouts.admin')

@push('head')
<link href="{{ asset('css/user.css') }}" rel="stylesheet">
@endpush

@section('admin-content')
<div class="page-header">
    @include('partials.breadcrumb', [
        'items' => [
            ['label' => 'Home', 'url' => route('dasbor.index')],
            ['label' => 'Surat Keluar']
        ]
    ])
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
@endsection

@push('scripts')
<script>
    const suratKeluarDataCurrentPage = {!! json_encode($suratKeluar) !!};

    /**
     * ANCHOR: Add Surat Keluar Handlers
     * Handle the add surat keluar form submission
     */
    const addSuratKeluarHandlers = () => {
        const addSuratKeluarForm = document.getElementById('addSuratKeluarForm');
        const addSuratKeluarSubmitBtn = document.getElementById('addSuratKeluarSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        if (addSuratKeluarForm) {
            addSuratKeluarForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors(addSuratKeluarForm);
                setLoadingState(true, addSuratKeluarSubmitBtn);

                try {
                    const formData = new FormData(addSuratKeluarForm);
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000);
                    const response = await fetchWithRetry(addSuratKeluarForm.action, {
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
                        addSuratKeluarForm.reset();
                        bootstrap.Modal.getInstance(document.getElementById('modalAddSuratKeluar')).hide();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        handleErrorResponse(data, addSuratKeluarForm);
                    }
                } catch (error) {
                    handleErrorResponse(error, addSuratKeluarForm);
                } finally {
                    setLoadingState(false, addSuratKeluarSubmitBtn);
                }
            });
        }
    }

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
        const pengirimBagianInput = document.getElementById('edit_pengirim_bagian_id');
        const ringkasanIsiInput = document.getElementById('edit_ringkasan_isi');
        const keteranganInput = document.getElementById('edit_keterangan');

        const suratKeluar = suratKeluarDataCurrentPage.data.find(surat => surat.id === suratKeluarId);
        const { id, nomor_surat, tanggal_surat, tanggal_keluar, perihal, tujuan, pengirim_bagian_id, ringkasan_isi, keterangan } = suratKeluar;

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

    addSuratKeluarHandlers();
    editSuratKeluarHandlers();
    deleteSuratKeluarHandlers();
</script>
@endpush
