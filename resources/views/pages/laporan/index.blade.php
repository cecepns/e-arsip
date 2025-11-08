@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    @include('partials.page-title', [
        'title' => 'Laporan',
        'subtitle' => 'Lihat dan cetak laporan surat masuk, surat keluar, dan disposisi berdasarkan filter yang ditentukan.'
    ])
</div>

<!-- Filter Form (Always Visible) -->
<div class="mb-3">
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filter Laporan
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.index') }}" id="filterForm" class="filter-form">
                <div class="row g-3">
                    <!-- Tanggal Mulai -->
                    <div class="col-md-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" id="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}">
                    </div>
                    
                    <!-- Tanggal Akhir -->
                    <div class="col-md-3">
                        <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" id="tanggal_akhir" value="{{ $filters['tanggal_akhir'] ?? '' }}">
                    </div>
                    
                    @if(Auth::user()?->role === 'Admin')
                        <!-- Bagian -->
                        <div class="col-md-3">
                            <label for="bagian_id" class="form-label">Bagian</label>
                            <select name="bagian_id" class="form-select" id="bagian_id">
                                <option value="">Semua Bagian</option>
                                @foreach($bagian as $b)
                                    <option value="{{ $b->id }}" {{ ($filters['bagian_id'] ?? '') == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama_bagian }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    
                    <!-- Jenis -->
                    <div class="col-md-3">
                        <label for="jenis" class="form-label">Jenis</label>
                        <select name="jenis" class="form-select" id="jenis" required>
                            <option value="surat_masuk" {{ ($filters['jenis'] ?? 'surat_masuk') == 'surat_masuk' ? 'selected' : '' }}>Surat Masuk</option>
                            <option value="surat_keluar" {{ ($filters['jenis'] ?? '') == 'surat_keluar' ? 'selected' : '' }}>Surat Keluar</option>
                            <option value="disposisi" {{ ($filters['jenis'] ?? '') == 'disposisi' ? 'selected' : '' }}>Disposisi</option>
                        </select>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="col-12 d-flex gap-2 filter-collapse-actions">
                        <button type="submit" class="btn btn-primary" id="applyFilterBtn">
                            <i class="fas fa-search"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($filters['tanggal_mulai']) && $filters['tanggal_mulai'] || isset($filters['tanggal_akhir']) && $filters['tanggal_akhir'] || isset($filters['bagian_id']) && $filters['bagian_id'] || isset($filters['jenis']))
<div class="alert alert-info mb-3">
    <i class="fas fa-filter me-2"></i>
    <strong>Filter Aktif:</strong>
    @if(isset($filters['tanggal_mulai']) && $filters['tanggal_mulai'])
        <span class="badge bg-primary me-1">Mulai: {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d/m/Y') }}</span>
    @endif
    @if(isset($filters['tanggal_akhir']) && $filters['tanggal_akhir'])
        <span class="badge bg-primary me-1">Akhir: {{ \Carbon\Carbon::parse($filters['tanggal_akhir'])->format('d/m/Y') }}</span>
    @endif
    @if(isset($filters['bagian_id']) && $filters['bagian_id'])
        @php
            $selectedBagian = $bagian->where('id', $filters['bagian_id'])->first();
        @endphp
        <span class="badge bg-info me-1">Bagian: {{ $selectedBagian->nama_bagian ?? 'Unknown' }}</span>
    @endif
    @if(isset($filters['jenis']))
        @php
            $jenisLabels = [
                'surat_masuk' => 'Surat Masuk',
                'surat_keluar' => 'Surat Keluar',
                'disposisi' => 'Disposisi'
            ];
        @endphp
        <span class="badge bg-success me-1">Jenis: {{ $jenisLabels[$filters['jenis']] ?? $filters['jenis'] }}</span>
    @endif
    @if(isset($data) && $data)
        <span class="ms-2">Ditemukan {{ $data->count() }} data</span>
    @endif
</div>
@endif

<!-- Action Buttons (Cetak & Ekspor PDF) -->
@if(isset($data) && $data->count() > 0)
<div class="mb-3 d-flex justify-content-end gap-2">
    <button class="btn btn-primary" id="btnCetak">
        <i class="fas fa-print me"></i> Cetak
    </button>
    <button class="btn btn-danger" id="btnEksporPDF">
        <i class="fas fa-file-pdf"></i> Ekspor PDF
    </button>
</div>
@endif

<!-- Table Content -->
@if(isset($data) && $data->count() > 0)
    @if($filters['jenis'] == 'surat_masuk')
        @include('partials.table', [
            'tableId' => 'laporanSuratMasukTable',
            'thead' => view()->make('pages.laporan._table.surat_masuk_head')->render(),
            'tbody' => view()->make('pages.laporan._table.surat_masuk_body', compact('data'))->render(),
        ])
    @elseif($filters['jenis'] == 'surat_keluar')
        @include('partials.table', [
            'tableId' => 'laporanSuratKeluarTable',
            'thead' => view()->make('pages.laporan._table.surat_keluar_head')->render(),
            'tbody' => view()->make('pages.laporan._table.surat_keluar_body', compact('data'))->render(),
        ])
    @elseif($filters['jenis'] == 'disposisi')
        @include('partials.table', [
            'tableId' => 'laporanDisposisiTable',
            'thead' => view()->make('pages.laporan._table.disposisi_head')->render(),
            'tbody' => view()->make('pages.laporan._table.disposisi_body', compact('data'))->render(),
        ])
    @endif
@elseif(isset($filters['jenis']))
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Tidak ada data ditemukan</strong> berdasarkan filter yang dipilih.
</div>
@else
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Pilih filter</strong> untuk menampilkan data laporan.
</div>
@endif

<!-- Modal Detail Surat Masuk -->
@include('partials.modal', [
    'id' => 'modalDetailSuratMasuk',
    'size' => 'modal-xl',
    'title' => 'Detail Surat Masuk',
    'body' => view()->make('pages.surat_masuk._detail_modal._body')->render(),
    'footer' => view()->make('pages.surat_masuk._detail_modal._footer')->render(),
])

<!-- Modal Detail Surat Keluar -->
@include('partials.modal', [
    'id' => 'modalDetailSuratKeluar',
    'size' => 'modal-xl',
    'title' => 'Detail Surat Keluar',
    'body' => view()->make('pages.surat_keluar._detail_modal._body')->render(),
    'footer' => view()->make('pages.surat_keluar._detail_modal._footer')->render(),
])

<!-- Modal Detail Disposisi -->
@include('partials.modal', [
    'id' => 'modalDetailDisposisi',
    'size' => 'modal-lg',
    'title' => 'Detail Disposisi',
    'body' => view()->make('pages.disposisi._detail_modal._body')->render(),
    'footer' => view()->make('pages.disposisi._detail_modal._footer')->render(),
])

@endsection

@push('scripts')
<script>
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
            
            window.location.href = '{{ route("laporan.index") }}?' + params.toString();
        });
    }

    // ANCHOR: Handle Cetak button
    const btnCetak = document.getElementById('btnCetak');
    if (btnCetak) {
        btnCetak.addEventListener('click', function() {
            handlePrintReport();
        });
    }

    // ANCHOR: Handle Ekspor PDF button
    const btnEksporPDF = document.getElementById('btnEksporPDF');
    if (btnEksporPDF) {
        btnEksporPDF.addEventListener('click', function() {
            handleExportPdf();
        });
    }

    // ANCHOR: Validate date range
    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalAkhir = document.getElementById('tanggal_akhir');
    
    if (tanggalMulai && tanggalAkhir) {
        tanggalMulai.addEventListener('change', function() {
            if (tanggalAkhir.value && this.value > tanggalAkhir.value) {
                tanggalAkhir.value = this.value;
            }
        });
        
        tanggalAkhir.addEventListener('change', function() {
            if (tanggalMulai.value && this.value < tanggalMulai.value) {
                tanggalMulai.value = this.value;
            }
        });
    }

    // ANCHOR: Show Detail Surat Masuk Modal
    window.showDetailSuratMasukModal = async (suratMasukId) => {
        try {
            const parentEl = document.getElementById('modalDetailSuratMasuk');
            
            // Show loading state
            const lampiranContent = parentEl.querySelector('#detail-lampiran-content');
            if (lampiranContent) {
                lampiranContent.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                        <p>Memuat detail surat masuk...</p>
                    </div>
                `;
            }

            // Fetch detail data
            const csrfToken = (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                document.querySelector('input[name="_token"]')?.value
            );

            const response = await fetch(`/surat-masuk/${suratMasukId}`, {
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
                populateSuratMasukDetailModal(data.suratMasuk);
            } else {
                throw new Error(data.message || 'Failed to load detail');
            }

        } catch (error) {
            console.error('Error loading detail:', error);
            const parentEl = document.getElementById('modalDetailSuratMasuk');
            const lampiranContent = parentEl.querySelector('#detail-lampiran-content');
            if (lampiranContent) {
                lampiranContent.innerHTML = `
                    <div class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Gagal memuat detail surat masuk</p>
                        <small class="text-muted">${error.message}</small>
                    </div>
                `;
            }
        }
    };

    // ANCHOR: Show Detail Surat Keluar Modal
    window.showDetailSuratKeluarModal = async (suratKeluarId) => {
        try {
            const parentEl = document.getElementById('modalDetailSuratKeluar');
            
            // Show loading state
            const lampiranContent = parentEl.querySelector('#detail-lampiran-content');
            if (lampiranContent) {
                lampiranContent.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                        <p>Memuat detail surat keluar...</p>
                    </div>
                `;
            }

            // Fetch detail data
            const csrfToken = (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                document.querySelector('input[name="_token"]')?.value
            );

            const response = await fetch(`/surat-keluar/${suratKeluarId}`, {
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
                populateSuratKeluarDetailModal(data.suratKeluar);
            } else {
                throw new Error(data.message || 'Failed to load detail');
            }

        } catch (error) {
            console.error('Error loading detail:', error);
            const parentEl = document.getElementById('modalDetailSuratKeluar');
            const lampiranContent = parentEl.querySelector('#detail-lampiran-content');
            if (lampiranContent) {
                lampiranContent.innerHTML = `
                    <div class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Gagal memuat detail surat keluar</p>
                        <small class="text-muted">${error.message}</small>
                    </div>
                `;
            }
        }
    };

    // ANCHOR: Show Detail Disposisi Modal
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
                populateDisposisiDetailModal(data.disposisi);
            } else {
                throw new Error(data.message || 'Failed to load detail');
            }

        } catch (error) {
            console.error('Error loading detail:', error);
            alert('Gagal memuat detail disposisi: ' + error.message);
        }
    };

    // ANCHOR: Populate Surat Masuk Detail Modal
    function populateSuratMasukDetailModal(suratMasuk) {
        // Store current surat masuk ID for action buttons
        window.currentDetailSuratMasukId = suratMasuk.id;
        window.currentDetailSuratMasuk = suratMasuk;
        
        const parentEl = document.getElementById('modalDetailSuratMasuk');
        
        // Basic information
        parentEl.querySelector('#detail-nomor-surat').textContent = suratMasuk.nomor_surat || '-';
        parentEl.querySelector('#detail-tanggal-surat').textContent = suratMasuk.tanggal_surat ? 
            new Date(suratMasuk.tanggal_surat).toLocaleDateString('id-ID') : '-';
        parentEl.querySelector('#detail-tanggal-terima').textContent = suratMasuk.tanggal_terima ? 
            new Date(suratMasuk.tanggal_terima).toLocaleDateString('id-ID') : '-';
        parentEl.querySelector('#detail-perihal').textContent = suratMasuk.perihal || '-';
        parentEl.querySelector('#detail-pengirim').textContent = suratMasuk.pengirim || '-';
        parentEl.querySelector('#detail-sifat-surat').textContent = suratMasuk.sifat_surat || '-';
        
        // Related information
        parentEl.querySelector('#detail-bagian-tujuan').textContent = 
            suratMasuk.tujuan_bagian?.nama_bagian || '-';
        parentEl.querySelector('#detail-user').textContent = 
            suratMasuk.user?.nama || '-';
        
        // Audit information
        parentEl.querySelector('#detail-updated-by').textContent = 
            suratMasuk.updater?.nama || '-';
        
        // Timestamps
        parentEl.querySelector('#detail-created-at').textContent = suratMasuk.created_at ? 
            new Date(suratMasuk.created_at).toLocaleString('id-ID') : '-';
        parentEl.querySelector('#detail-updated-at').textContent = suratMasuk.updated_at ? 
            new Date(suratMasuk.updated_at).toLocaleString('id-ID') : '-';

        // Ringkasan isi
        const ringkasanSection = parentEl.querySelector('#detail-ringkasan-section');
        const ringkasanContent = parentEl.querySelector('#detail-ringkasan-isi');
        if (suratMasuk.ringkasan_isi) {
            ringkasanContent.textContent = suratMasuk.ringkasan_isi;
            ringkasanSection.style.display = 'block';
        } else {
            ringkasanSection.style.display = 'none';
        }

        // Keterangan
        const keteranganSection = parentEl.querySelector('#detail-keterangan-section');
        const keteranganContent = parentEl.querySelector('#detail-keterangan');
        if (suratMasuk.keterangan) {
            keteranganContent.textContent = suratMasuk.keterangan;
            keteranganSection.style.display = 'block';
        } else {
            keteranganSection.style.display = 'none';
        }

        // Lampiran
        populateLampiranDetail(suratMasuk.lampiran || [], parentEl);
        
        // Disposisi
        populateDisposisiDetail(suratMasuk.disposisi || [], parentEl);
    }

    // ANCHOR: Populate Surat Keluar Detail Modal
    function populateSuratKeluarDetailModal(suratKeluar) {
        console.log('Surat Keluar lampiran data:', JSON.stringify(suratKeluar));
        // Store current surat keluar ID for action buttons
        window.currentDetailSuratKeluarId = suratKeluar.id;
        
        const parentEl = document.getElementById('modalDetailSuratKeluar');
        
        // Basic information
        parentEl.querySelector('#detail-nomor-surat').textContent = suratKeluar.nomor_surat || '-';
        parentEl.querySelector('#detail-tanggal-surat').textContent = suratKeluar.tanggal_surat ? 
            new Date(suratKeluar.tanggal_surat).toLocaleDateString('id-ID') : '-';
        parentEl.querySelector('#detail-tanggal-keluar').textContent = suratKeluar.tanggal_keluar ? 
            new Date(suratKeluar.tanggal_keluar).toLocaleDateString('id-ID') : '-';
        parentEl.querySelector('#detail-perihal').textContent = suratKeluar.perihal || '-';
        
        // Sifat surat dengan badge
        const sifatSuratElement = parentEl.querySelector('#detail-sifat-surat');
        const sifatSurat = suratKeluar.sifat_surat || 'Biasa';
        let badgeClass = 'badge-secondary';
        switch(sifatSurat) {
            case 'Segera':
                badgeClass = 'badge-warning';
                break;
            case 'Penting':
                badgeClass = 'badge-danger';
                break;
            case 'Rahasia':
                badgeClass = 'badge-dark';
                break;
            default:
                badgeClass = 'badge-secondary';
        }
        sifatSuratElement.innerHTML = `<span class="badge ${badgeClass}">${sifatSurat}</span>`;
        
        parentEl.querySelector('#detail-tujuan').textContent = suratKeluar.tujuan || '-';
        
        // Related information
        parentEl.querySelector('#detail-bagian-pengirim').textContent = 
            suratKeluar.pengirim_bagian?.nama_bagian || '-';
        
        // Audit information
        parentEl.querySelector('#detail-user').textContent = 
            suratKeluar.user?.username || '-';
        parentEl.querySelector('#detail-created-at').textContent = 
            formatDateTimeForDisplay(suratKeluar.created_at);
        parentEl.querySelector('#detail-updated-by').textContent = 
            suratKeluar.updater?.username || '-';
        parentEl.querySelector('#detail-updated-at').textContent = 
            formatDateTimeForDisplay(suratKeluar.updated_at);

        // Ringkasan isi
        const ringkasanSection = parentEl.querySelector('#detail-ringkasan-section');
        const ringkasanContent = parentEl.querySelector('#detail-ringkasan-isi');
        if (suratKeluar.ringkasan_isi) {
            ringkasanContent.textContent = suratKeluar.ringkasan_isi;
            ringkasanSection.style.display = 'block';
        } else {
            ringkasanSection.style.display = 'none';
        }

        // Keterangan
        const keteranganSection = parentEl.querySelector('#detail-keterangan-section');
        const keteranganContent = parentEl.querySelector('#detail-keterangan');
        if (suratKeluar.keterangan) {
            keteranganContent.textContent = suratKeluar.keterangan;
            keteranganSection.style.display = 'block';
        } else {
            keteranganSection.style.display = 'none';
        }

        // Lampiran
        populateLampiranDetail(suratKeluar.lampiran || [], parentEl);
    }

    // ANCHOR: Populate Disposisi Detail Modal
    function populateDisposisiDetailModal(disposisi) {
        const parentEl = document.getElementById('modalDetailDisposisi');
        
        parentEl.querySelector('#detailNomorSurat').textContent = disposisi.surat_masuk?.nomor_surat || '-';
        parentEl.querySelector('#detailTanggalSurat').textContent = disposisi.surat_masuk?.tanggal_surat ? 
            new Date(disposisi.surat_masuk.tanggal_surat).toLocaleDateString('id-ID') : '-';
        parentEl.querySelector('#detailPerihal').textContent = disposisi.surat_masuk?.perihal || '-';
        parentEl.querySelector('#detailPengirim').textContent = disposisi.surat_masuk?.pengirim || '-';
        parentEl.querySelector('#detailDisposisiDari').textContent = disposisi.surat_masuk?.tujuan_bagian?.kepala_bagian?.nama || 'Belum ditentukan';
        parentEl.querySelector('#detailDisposisiDariBagian').textContent = disposisi.surat_masuk?.tujuan_bagian?.nama_bagian || '-';
        parentEl.querySelector('#detailDisposisiKepada').textContent = disposisi.tujuan_bagian?.kepala_bagian?.nama || 'Belum ditentukan';
        parentEl.querySelector('#detailDisposisiKepadaBagian').textContent = disposisi.tujuan_bagian?.nama_bagian || '-';
        parentEl.querySelector('#detailSifatSurat').textContent = disposisi.surat_masuk?.sifat_surat || '-';
        parentEl.querySelector('#detailTanggalDisposisi').textContent = disposisi.tanggal_disposisi ? 
            new Date(disposisi.tanggal_disposisi).toLocaleDateString('id-ID') : '-';
        parentEl.querySelector('#detailBatasWaktu').textContent = disposisi.batas_waktu ? 
            new Date(disposisi.batas_waktu).toLocaleDateString('id-ID') : '-';
        parentEl.querySelector('#detailInstruksi').textContent = disposisi.isi_instruksi || '-';
        parentEl.querySelector('#detailCatatan').textContent = disposisi.catatan || '-';
        parentEl.querySelector('#detailStatus').textContent = disposisi.status || '-';
        parentEl.querySelector('#detailDibuatOleh').textContent = disposisi.user?.nama || '-';
        parentEl.querySelector('#detailDibuatTanggal').textContent = disposisi.created_at ? 
            new Date(disposisi.created_at).toLocaleDateString('id-ID') : '-';
    }

    // ANCHOR: Format Date for Display
    const formatDateForDisplay = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    // ANCHOR: Format DateTime for Display
    const formatDateTimeForDisplay = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    // ANCHOR: Populate Lampiran Detail
    const populateLampiranDetail = (lampiran, parentEl) => {
        const lampiranContent = parentEl.querySelector('#detail-lampiran-content');
        
        if (lampiran.length === 0) {
            lampiranContent.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-paperclip fa-2x mb-2"></i>
                    <p>Tidak ada lampiran</p>
                </div>
            `;
            return;
        }

        let lampiranHtml = '<div class="row">';
        
        lampiran.forEach((file, index) => {
            const isPdf = file.nama_file.toLowerCase().endsWith('.pdf');
            const iconClass = isPdf ? 'fa-file-pdf text-danger' : 'fa-file-alt text-primary';
            const downloadUrl = `/storage/${file.path_file}`;
            
            lampiranHtml += `
                <div class="col-md-6 mb-3">
                    <div class="card border">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas ${iconClass} fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1 text-truncate" title="${file.nama_file}">
                                        ${file.nama_file}
                                    </h6>
                                    <small class="text-muted">
                                        ${file.tipe_lampiran === 'utama' ? 'Lampiran Utama' : 'Dokumen Pendukung'}
                                    </small>
                                </div>
                                <div class="ms-2">
                                    <a href="${downloadUrl}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Download ${file.nama_file}"
                                       download="${file.nama_file}">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        lampiranHtml += '</div>';
        lampiranContent.innerHTML = lampiranHtml;
    }

    // ANCHOR: Populate Disposisi Detail
    const populateDisposisiDetail = (disposisi, parentEl) => {
        const disposisiContent = parentEl.querySelector('#detail-disposisi-content');
        const disposisiSection = parentEl.querySelector('#detail-disposisi-section');
        
        if (disposisi.length === 0) {
            disposisiSection.style.display = 'none';
            return;
        }

        let disposisiHtml = '';
        
        disposisi.forEach((disp, index) => {
            const statusBadgeClass = 
                disp.status === 'Menunggu' ? 'bg-warning' :
                disp.status === 'Dikerjakan' ? 'bg-info' :
                disp.status === 'Selesai' ? 'bg-success' : 'bg-secondary';
            
            // Get kepala bagian information
            const kepalaBagianTujuan = disp.tujuan_bagian?.kepala_bagian?.nama || '-';
            const kepalaBagianPengirim = window.currentDetailSuratMasuk?.tujuan_bagian?.kepala_bagian?.nama || '-';
            
            disposisiHtml += `
                <div class="mb-4">
                    <div class="card border">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-share-alt me-2"></i>Disposisi ${index + 1}
                                </h6>
                                <span class="badge ${statusBadgeClass}">${disp.status}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Dibuat Oleh:</span>
                                            <span class="text-secondary">${disp.user?.nama || '-'}</span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Disposisi Dari:</span>
                                            <span class="text-secondary">${kepalaBagianPengirim} (${window.currentDetailSuratMasuk?.tujuan_bagian?.nama_bagian || '-'})</span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Dibuat Pada:</span>
                                            <span class="text-muted">${disp.created_at ? new Date(disp.created_at).toLocaleString('id-ID') : '-'}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Disposisi Kepada:</span>
                                            <span class="text-secondary">${kepalaBagianTujuan} (${disp.tujuan_bagian?.nama_bagian || '-'})</span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Instruksi:</span>
                                            <span class="text-dark">${disp.isi_instruksi || '-'}</span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Tanggal Disposisi:</span>
                                            <span class="text-secondary">${disp.tanggal_disposisi ? new Date(disp.tanggal_disposisi).toLocaleDateString('id-ID') : '-'}</span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="fw-semibold text-dark">Batas Waktu:</span>
                                            <span class="text-secondary">${disp.batas_waktu ? new Date(disp.batas_waktu).toLocaleDateString('id-ID') : '-'}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            ${disp.catatan ? `
                                <div class="mt-3 pt-3 border-top">
                                    <p class="mb-2">
                                        <span class="fw-semibold text-dark">Catatan:</span>
                                    </p>
                                    <div class="bg-light p-3 rounded">
                                        <p class="mb-0 text-dark">${disp.catatan}</p>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        disposisiContent.innerHTML = disposisiHtml;
        disposisiSection.style.display = 'block';
    }

    // ANCHOR: Handle Print Report Function
    function handlePrintReport() {
        try {
            // Get current filter values
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }
            
            // Open print page in new window
            const printUrl = '{{ route("laporan.print") }}?' + params.toString();
            const printWindow = window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
            
            if (printWindow) {
                printWindow.focus();
                
                // Auto print when page loads
                printWindow.onload = function() {
                    setTimeout(function() {
                        printWindow.print();
                    }, 500);
                };
            } else {
                // Fallback if popup blocked
                alert('Popup diblokir. Silakan izinkan popup untuk fitur cetak.');
            }
        } catch (error) {
            console.error('Error opening print window:', error);
            alert('Terjadi kesalahan saat membuka halaman cetak.');
        }
    }

    // ANCHOR: Handle Export PDF Function
    function handleExportPdf() {
        try {
            // Show loading state
            const btnEksporPDF = document.getElementById('btnEksporPDF');
            const originalText = btnEksporPDF.innerHTML;
            btnEksporPDF.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengekspor PDF...';
            btnEksporPDF.disabled = true;

            // Get current filter values
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }
            
            // Create download link
            const exportUrl = '{{ route("laporan.export-pdf") }}?' + params.toString();
            
            // Create temporary link element
            const link = document.createElement('a');
            link.href = exportUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Trigger download
            link.click();
            
            // Clean up
            document.body.removeChild(link);
            
            // Reset button state after delay
            setTimeout(function() {
                btnEksporPDF.innerHTML = originalText;
                btnEksporPDF.disabled = false;
            }, 2000);
            
        } catch (error) {
            console.error('Error exporting PDF:', error);
            alert('Terjadi kesalahan saat mengekspor PDF.');
            
            // Reset button state on error
            const btnEksporPDF = document.getElementById('btnEksporPDF');
            btnEksporPDF.innerHTML = '<i class="fas fa-file-pdf"></i> Ekspor PDF';
            btnEksporPDF.disabled = false;
        }
    }
});
</script>
@endpush
