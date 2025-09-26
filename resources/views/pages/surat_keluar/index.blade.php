@extends('layouts.admin')

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
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formSuratKeluarModal">
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
    'thead' => view('pages.surat_keluar._table_head')->render(),
    'tbody' => view('pages.surat_keluar._table_body', ['suratKeluar' => $suratKeluar])->render(),
])

@include('partials.pagination', [
    'currentPage' => 1,
    'totalPages' => 2,
    'baseUrl' => route('surat_keluar.index'),
    'showInfo' => 'Menampilkan 1-5 dari ' . $suratKeluar->count() . ' surat keluar'
])

@include('partials.modal', [
    'id' => 'formSuratKeluarModal',
    'size' => 'modal-xl',
    'title' => '<span id="modalTitle">Tambah Surat Keluar</span>',
    'body' => view('pages.surat_keluar._form_modal', compact('bagian'))->render(),
])

@include('partials.modal', [
    'type' => 'danger',
    'id' => 'deleteSuratKeluarModal',
    'title' => 'Hapus Surat Keluar',
    'size' => 'modal-md',
    'body' => view('pages.surat_keluar._delete_modal')->render(),
    'footer' => view('pages.surat_keluar._delete_modal_footer')->render(),
])
</div>
@endsection

@push('scripts')
<script>
function editSuratKeluar(button) {
    // Ambil data dari atribut data
    const id = button.getAttribute('data-id');
    const nomor = button.getAttribute('data-nomor');
    const tujuan = button.getAttribute('data-tujuan');
    const perihal = button.getAttribute('data-perihal');
    const bagian = button.getAttribute('data-bagian');
    const keterangan = button.getAttribute('data-keterangan');

    document.getElementById('modalTitle').textContent = 'Edit Surat Keluar';
    const form = document.getElementById('formSuratKeluar');
    form.action = `/surat-keluar/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('submitBtn').textContent = 'Update';
    document.getElementById('surat_keluar_id').value = id;
    document.getElementById('nomor_surat').value = nomor;
    document.getElementById('tujuan').value = tujuan;
    document.getElementById('perihal').value = perihal;
    document.getElementById('pengirim_bagian_id').value = bagian;
    document.getElementById('keterangan').value = keterangan;
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('formSuratKeluarModal');
    modal.addEventListener('hidden.bs.modal', function() {
        resetForm();
    });
    document.querySelector('[data-bs-target="#formSuratKeluarModal"]').addEventListener('click', function() {
        resetForm();
    });
});

function resetForm() {
    document.getElementById('modalTitle').textContent = 'Tambah Surat Keluar';
    const form = document.getElementById('formSuratKeluar');
    form.action = '{{ route("surat_keluar.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('submitBtn').textContent = 'Simpan';
    document.getElementById('surat_keluar_id').value = '';
    document.getElementById('nomor_surat').value = '';
    document.getElementById('tujuan').value = '';
    document.getElementById('perihal').value = '';
    document.getElementById('pengirim_bagian_id').value = '';
    document.getElementById('keterangan').value = '';
}

function deleteSuratKeluar(button) {
    const id = button.getAttribute('data-id');
    const nomor = button.getAttribute('data-nomor');
    document.getElementById('deleteSuratKeluarName').textContent = nomor;
    const form = document.getElementById('formDeleteSuratKeluar');
    form.action = `/surat-keluar/${id}`;
}
</script>
@endpush