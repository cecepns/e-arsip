@extends('layouts.admin')

@push('head')
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endpush

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
    <form class="d-flex" style="max-width:300px;">
        <input type="text" class="form-control me-2" placeholder="Cari nama bagian...">
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
    </form>
</div>

@include('partials.table', [
    'tableId' => 'bagianTable',
    'tableClass' => 'table table-striped table-hover',
    'thead' => view()->make('pages.bagian._table_head')->render(),
    'tbody' => view()->make('pages.bagian._table_body')->render(),
])

@include('partials.pagination', [
    'currentPage' => 1,
    'totalPages' => 2,
    'baseUrl' => '#',
    'showInfo' => 'Menampilkan 1-5 dari 8 bagian'
])

{{-- Modal Form Tambah/Edit Bagian --}}
<div class="modal fade" id="modalBagianForm" tabindex="-1" aria-labelledby="modalBagianFormLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBagianFormLabel">Tambah/Edit Bagian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="no" class="form-label">No</label>
            <input type="text" class="form-control" id="no" value="">
          </div>
          <div class="mb-3">
            <label for="nama_bagian" class="form-label">Nama Bagian</label>
            <input type="text" class="form-control" id="nama_bagian" value="">
          </div>
          <div class="mb-3">
            <label for="kepala_bagian" class="form-label">Kepala Bagian</label>
            <input type="text" class="form-control" id="kepala_bagian" value="">
          </div>
          <div class="mb-3">
            <label for="jumlah_staff" class="form-label">Jumlah Staff</label>
            <input type="number" class="form-control" id="jumlah_staff" value="">
          </div>
          <div class="mb-3">
            <label class="form-label">Surat</label>
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status">
              <option>Aktif</option>
              <option>Tidak Aktif</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Detail Bagian --}}
<div class="modal fade" id="modalBagianDetail" tabindex="-1" aria-labelledby="modalBagianDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBagianDetailLabel">Detail Bagian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <strong>Nama Bagian:</strong> Keuangan<br>
          <strong>Kepala Bagian:</strong> Siti Aminah<br>
          <strong>Jumlah Staff:</strong> 8<br>
          <strong>Status:</strong> Aktif<br>
        </div>
        <div class="row">
          <div class="col-md-6">
            <h6>Surat Masuk</h6>
            <ul class="list-group mb-3">
              <li class="list-group-item">SM-001 - 12/09/2025 - Undangan Rapat</li>
              <li class="list-group-item">SM-014 - 15/09/2025 - Permintaan Data</li>
            </ul>
          </div>
          <div class="col-md-6">
            <h6>Surat Keluar</h6>
            <ul class="list-group mb-3">
              <li class="list-group-item">SK-002 - 13/09/2025 - Laporan Keuangan</li>
              <li class="list-group-item">SK-005 - 16/09/2025 - Permohonan Dana</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection
