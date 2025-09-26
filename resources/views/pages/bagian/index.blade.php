@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    @include('partials.breadcrumb', [
        'items' => [
            ['label' => 'Home', 'url' => '#'],
            ['label' => 'Data Bagian']
        ]
    ])
    @include('partials.page-title', [
        'title' => 'Manajemen Bagian',
        'subtitle' => 'Kelola unit kerja/divisi di lingkungan instansi.'
    ])
</div>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBagianForm">
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
    - Ditemukan {{ $bagian->count() }} bagian
</div>
@endif

@include('partials.table', [
    'tableId' => 'bagianTable',
    'thead' => view()->make('pages.bagian._table_head')->render(),
    'tbody' => view()->make('pages.bagian._table_body', compact('bagian'))->render(),
])

@include('partials.pagination', [
    'currentPage' => 1,
    'totalPages' => 2,
    'baseUrl' => '#',
    'showInfo' => 'Menampilkan 1-5 dari 8 bagian'
])

@include('partials.modal', [
    'id' => 'modalBagianForm',
    'size' => 'modal-md',
    'title' => '<span id="modalTitle">Tambah Bagian</span>',
    'body' => view()->make('pages.bagian._form_modal')->render(),
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
    'title' => 'Konfirmasi Hapus User',
    'size' => 'modal-md',
    'body' => '<p>Apakah Anda yakin ingin menghapus bagian <strong id="deleteBagianName"></strong>?</p><p class="text-muted">Data akan dihapus secara soft delete untuk menjaga integritas data relasi.</p>',
    'footer' => '<form id="deleteBagianForm" method="POST" style="display: inline;"><input type="hidden" name="_method" value="DELETE"><input type="hidden" name="_token" value="' . csrf_token() . '"><button type="submit" class="btn btn-danger">Ya, Hapus</button></form><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>',
])
@endsection

@push('scripts')
<script>
function editBagian(button) {
    // Ambil data dari atribut data
    const id = button.getAttribute('data-id');
    const nama = button.getAttribute('data-nama');
    const kepala = button.getAttribute('data-kepala');
    const status = button.getAttribute('data-status');
    const keterangan = button.getAttribute('data-keterangan');

    // Update modal title
    console.log(document.getElementById('modalTitle'));
    document.getElementById('modalTitle').textContent = 'Edit Bagian';
    
    // Update form action dan method
    const form = document.getElementById('bagianForm');
    form.action = `/bagian/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    
    // Update tombol submit
    document.getElementById('submitBtn').textContent = 'Update';
    
    // Populate form dengan data yang ada
    document.getElementById('bagian_id').value = id;
    document.getElementById('nama_bagian').value = nama;
    document.getElementById('kepala_bagian').value = kepala || '';
    document.getElementById('status').value = status;
    document.getElementById('keterangan').value = keterangan || '';
}

// Reset form ketika modal ditutup atau dibuka untuk tambah
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalBagianForm');
    
    // Reset form ketika modal ditutup
    modal.addEventListener('hidden.bs.modal', function() {
        resetForm();
    });
    
    // Reset form ketika tombol tambah diklik
    document.querySelector('[data-bs-target="#modalBagianForm"]').addEventListener('click', function() {
        resetForm();
    });
});

function resetForm() {
    // Reset modal title
    document.getElementById('modalTitle').textContent = 'Tambah Bagian';
    
    // Reset form action dan method
    const form = document.getElementById('bagianForm');
    form.action = '{{ route("bagian.store") }}';
    document.getElementById('formMethod').value = 'POST';
    
    // Reset tombol submit
    document.getElementById('submitBtn').textContent = 'Simpan';
    
    // Clear form fields
    document.getElementById('bagian_id').value = '';
    document.getElementById('nama_bagian').value = '';
    document.getElementById('kepala_bagian').value = '';
    document.getElementById('status').value = 'Aktif';
    document.getElementById('keterangan').value = '';
}

function deleteBagian(button) {
    // Ambil data dari atribut data
    const id = button.getAttribute('data-id');
    const nama = button.getAttribute('data-nama');
    
    // Update nama bagian di modal
    document.getElementById('deleteBagianName').textContent = nama;
    
    // Update form action
    const form = document.getElementById('deleteBagianForm');
    form.action = `/bagian/${id}`;
}


// // Search functionality enhancements
// document.addEventListener('DOMContentLoaded', function() {
//     const searchInput = document.querySelector('input[name="search"]');
//     const searchForm = document.querySelector('form[method="GET"]');
    
//     // Auto-submit form when user stops typing (debounced)
//     let searchTimeout;
//     if (searchInput) {
//         searchInput.addEventListener('input', function() {
//             clearTimeout(searchTimeout);
//             searchTimeout = setTimeout(function() {
//                 if (searchInput.value.length >= 2 || searchInput.value.length === 0) {
//                     searchForm.submit();
//                 }
//             }, 500); // Wait 500ms after user stops typing
//         });
//     }
    
//     // Focus search input when pressing Ctrl+K
//     document.addEventListener('keydown', function(e) {
//         if (e.ctrlKey && e.key === 'k') {
//             e.preventDefault();
//             if (searchInput) {
//                 searchInput.focus();
//                 searchInput.select();
//             }
//         }
//     });
// });
</script>
@endpush
