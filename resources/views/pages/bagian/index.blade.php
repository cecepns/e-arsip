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
        <i class="fas fa-plus"></i> Tambah Bagian
    </button>
    <form class="d-flex" style="max-width:300px;" method="GET" action="{{ route('bagian.index') }}">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari nama bagian..." value="{{ $query ?? '' }}">
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
        @if(isset($query) && $query)
            <a href="{{ route('bagian.index') }}" class="btn btn-outline-danger ms-1" title="Clear search">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>
</div>

@if(isset($query) && $query)
<div class="alert alert-info mb-3">
    <i class="fas fa-search me-2"></i> 
    Hasil pencarian untuk: <strong>"{{ $query }}"</strong> 
    - Ditemukan {{ $bagian->total() }} bagian
</div>
@endif

@include('partials.table', [
    'tableId' => 'bagianTable',
    'thead' => view()->make('pages.bagian._table._head')->render(),
    'tbody' => view()->make('pages.bagian._table._body', compact('bagian'))->render(),
])

@include('partials.pagination', [
    'currentPage' => $bagian->currentPage(),
    'totalPages' => $bagian->lastPage(),
    'baseUrl' => $bagian->url($bagian->currentPage()),
    'showInfo' => "Menampilkan {$bagian->firstItem()}-{$bagian->lastItem()} dari {$bagian->total()} bagian"
])

@include('partials.modal', [
    'id' => 'modalAddBagian',
    'size' => 'modal-md',
    'title' => 'Tambah Bagian',
    'body' => view('pages.bagian._form_modal._add_form')->render(),
])

@include('partials.modal', [
    'id' => 'modalEditBagian',
    'size' => 'modal-md',
    'title' => 'Edit Bagian',
    'body' => view('pages.bagian._form_modal._edit_form')->render(),
])

@include('partials.modal', [
    'id' => 'modalBagianDetail',
    'title' => 'Detail Bagian',
    'size' => 'modal-xl',
    'body' => view()->make('pages.bagian._detail_modal')->render(),
])

@include('partials.modal', [
    'type' => 'danger',
    'id' => 'modalDeleteBagian',
    'title' => 'Konfirmasi Hapus Bagian',
    'size' => 'modal-md',
    'body' => view()->make('pages.bagian._delete_modal._body')->render(),
    'footer' => view()->make('pages.bagian._delete_modal._footer')->render(),
])
@endsection

@push('scripts')
<script>
    const bagianDataCurrentPage = {!! json_encode($bagian->items()) !!};

    /**
     * ANCHOR: Add Bagian Handlers
     * Handle the add bagian form submission
     */
    const addBagianHandlers = () => {
        const addBagianForm = document.getElementById('addBagianForm');
        const addBagianSubmitBtn = document.getElementById('addBagianSubmitBtn');
        const addBagianCancelBtn = document.getElementById('addBagianCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        addBagianForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(addBagianForm);
            setLoadingState(true, addBagianSubmitBtn);

            try {
                const formData = new FormData(addBagianForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(addBagianForm.action, {
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
                    addBagianForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalAddBagian')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, addBagianForm);
                }
            } catch (error) {
                handleErrorResponse(error, addBagianForm);
            } finally {
                setLoadingState(false, addBagianSubmitBtn);
            }
        });
    }
    

    /**
     * ANCHOR: Show Edit Bagian Modal
     * Show the edit bagian modal
     * @param {number} bagianId - The id of the bagian to edit
     */
    const showEditBagianModal = (bagianId) => {
        const editBagianForm = document.getElementById('editBagianForm');
        const idInput = document.getElementById('edit_bagian_id');
        const namaInput = document.getElementById('edit_nama_bagian');
        const kepalaInput = document.getElementById('edit_kepala_bagian_user_id');
        const statusInput = document.getElementById('edit_status');
        const keteranganInput = document.getElementById('edit_keterangan');

        const bagian = bagianDataCurrentPage.find(bagian => bagian.id === bagianId);
        const { id, nama_bagian, kepala_bagian_user_id, status, keterangan } = bagian;

        idInput.value = id;
        namaInput.value = nama_bagian || '';
        kepalaInput.value = kepala_bagian_user_id || '';
        statusInput.value = status || '';
        keteranganInput.value = keterangan || '';

        editBagianForm.action = `/bagian/${id}`;
    }

    /**
     * ANCHOR: Show Delete Bagian Modal
     * Show the delete bagian modal
     * @param {number} bagianId - The id of the bagian to delete
     */
    const showDeleteBagianModal = (bagianId) => {
        const deleteBagianName = document.getElementById('deleteBagianName');
        const deleteBagianForm = document.getElementById('deleteBagianForm');

        const bagian = bagianDataCurrentPage.find(bagian => bagian.id === bagianId);
        const { id, nama_bagian } = bagian;

        deleteBagianName.textContent = nama_bagian;
        deleteBagianForm.action = `/bagian/${id}`;
    }

    /**
     * ANCHOR: Show Detail Bagian Modal
     * Show the detail bagian modal
     * @param {number} bagianId - The id of the bagian to show the details of
     */
    const showDetailBagianModal = (bagianId) => {
        const bagian = bagianDataCurrentPage.find(bagian => bagian.id === bagianId);
        console.log('Showing details for bagian:', bagian);
    }

    /**
     * ANCHOR: Edit Bagian Handlers
     * Handle the edit bagian form submission
     */
    const editBagianHandlers = () => {
        const editBagianForm = document.getElementById('editBagianForm');
        const editBagianSubmitBtn = document.getElementById('editBagianSubmitBtn');
        const editBagianCancelBtn = document.getElementById('editBagianCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        editBagianForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(editBagianForm);
            setLoadingState(true, editBagianSubmitBtn);

            try {
                const formData = new FormData(editBagianForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(editBagianForm.action, {
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
                    editBagianForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalEditBagian')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, editBagianForm);
                }
            } catch (error) {
                handleErrorResponse(error, editBagianForm);
            } finally {
                setLoadingState(false, editBagianSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Delete Bagian Handlers
     * Handle the delete bagian form submission
     */
    const deleteBagianHandlers = () => {
        const deleteBagianForm = document.getElementById('deleteBagianForm');
        const deleteBagianSubmitBtn = document.getElementById('deleteBagianSubmitBtn');
        const deleteBagianCancelBtn = document.getElementById('deleteBagianCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        deleteBagianForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(deleteBagianForm);
            setLoadingState(true, deleteBagianSubmitBtn);

            try {
                const formData = new FormData(deleteBagianForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(deleteBagianForm.action, {
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
                    deleteBagianForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalDeleteBagian')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, deleteBagianForm);
                }
            } catch (error) {
                handleErrorResponse(error, deleteBagianForm);
            } finally {
                setLoadingState(false, deleteBagianSubmitBtn);
            }
        });
    }

    // ANCHOR: Run all handlers
    addBagianHandlers();
    editBagianHandlers();
    deleteBagianHandlers();
</script>
@endpush
