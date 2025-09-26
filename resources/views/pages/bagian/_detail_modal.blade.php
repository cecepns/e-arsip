{{-- SECTION: Informasi Bagian --}}
<div class="mb-3">
    <h5 class="mb-3">Informasi Bagian</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <p class="mb-2"><span class="fw-semibold">Nama Bagian:</span> Keuangan</p>
                <p class="mb-2"><span class="fw-semibold">Kepala Bagian:</span> Siti Aminah</p>
                <p class="mb-2"><span class="fw-semibold">Jumlah Staff:</span> 8</p>
                <p class="mb-2"><span class="fw-semibold">Status:</span> Aktif</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <p><span class="fw-semibold">Deskripsi:</span><br> Bagian Keuangan adalah bagian yang bertugas untuk mengelola keuangan instansi.</p>
            </div>
            <div class="mb-3">
                <p><span class="fw-semibold">Total Surat:</span> 8</p>
            </div>
        </div>
    </div>
</div>

{{-- !SECTION: Informasi Bagian --}}
<div class="mb-3">
    <h5 class="mb-3">Surat Masuk/Keluar</h5>
    @include('partials.table', [
        'tableId' => 'bagianTableSurat',
        'tableClass' => 'table table-striped table-hover',
        'thead' => view()->make('pages.bagian._surat_table._head')->render(),
        'tbody' => view()->make('pages.bagian._surat_table._body')->render(),
    ])
</div>