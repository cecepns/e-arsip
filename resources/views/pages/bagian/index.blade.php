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
    // ANCHOR: Make data globally accessible for modals
    window.bagianDataCurrentPage = {!! json_encode($bagian->items()) !!};
    window.usersData = {!! json_encode($users) !!};

    


    /**
     * ANCHOR: Show Delete Bagian Modal
     * Show the delete bagian modal
     * @param {number} bagianId - The id of the bagian to delete
     */
    const showDeleteBagianModal = (bagianId) => {
        const deleteBagianName = document.getElementById('deleteBagianName');
        const deleteBagianForm = document.getElementById('deleteBagianForm');

        const bagian = window.bagianDataCurrentPage.find(bagian => bagian.id === bagianId);
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
        const bagian = window.bagianDataCurrentPage.find(bagian => bagian.id === bagianId);
        console.log('Showing details for bagian:', bagian);
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
    deleteBagianHandlers();
</script>
@endpush
