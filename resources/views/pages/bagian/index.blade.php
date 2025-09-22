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
    <form class="d-flex" style="max-width:300px;">
        <input type="text" class="form-control me-2" placeholder="Cari nama bagian...">
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
    </form>
</div>

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
    'title' => 'Tambah Bagian',
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
    'title' => 'Detail Bagian',
    'size' => 'modal-xl',
    'body' => 'Apakah Anda yakin ingin menghapus bagian ini?',
    'footer' => '<button class="btn btn-danger">Ya</button><button class="btn btn-secondary">Tidak</button>',
])
@endsection
