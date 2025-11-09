@extends('layouts.admin')

@section('admin-content')
{{-- SECTION: Page Header --}}
<div class="page-header">
    {{-- ANCHOR: Page Title & Subtitle --}}
    @include('partials.page-title', [
        'title' => 'Dashboard',
        'subtitle' => 'Kelola surat masuk dan surat keluar dengan mudah.'
    ])
</div>
{{-- !SECTION: Page Header --}}

{{-- SECTION: Statistics Cards Counter --}}
<div class="row g-3 mb-4">
    @foreach($statistics as $stat)
        <div class="col-lg-4 col-md-6">
            <div class="stat-card">
                <div class="card-icon-wrapper {{ $stat['bg'] }}">
                    <i class="{{ $stat['icon'] }}"></i>
                </div>
                <h6 class="card-title">{{ $stat['title'] }}</h6>
                <div class="stat-number">{{ $stat['number'] }}</div>
            </div>
        </div>
    @endforeach
</div>
{{-- !SECTION: Statistics Cards Counter --}}

{{-- SECTION: Distribution Statistics Chart --}}
<div class="chart-section">
    {{-- ANCHOR: Statistics Chart Header --}}
    <div class="chart-header">
        <div>
            <h3 class="chart-title">Statistik Distribusi Surat</h3>
            <p class="chart-subtitle">Monitoring distribusi surat masuk, keluar dan disposisi</p>
        </div>
        <div class="time-filter">
            <button class="filter-btn">7 Hari Terakhir</button>
            <button class="filter-btn active">30 Hari Terakhir</button>
            <button class="filter-btn">90 Hari Terakhir</button>
            <div class="dropdown">
                <button class="filter-btn dropdown-toggle" type="button" id="yearDropdown" data-bs-toggle="dropdown">
                    Tahun
                </button>
                @php
                    $currentYear = now()->year;
                @endphp
                <ul class="dropdown-menu">
                    @for($year = $currentYear; $year > $currentYear - 5; $year--)
                        <li><a class="dropdown-item" href="#" data-year="{{ $year }}">{{ $year }}</a></li>
                    @endfor
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- ANCHOR: Statistics Chart Content "Surat per Bagian" - Only for Admin --}}
        @if($isAdmin)
        <div class="col-lg-6">
            <div class="chart-left">
                <h4 class="section-title">Statistik Surat per Bagian</h4>
                <div class="dept-list">
                    @forelse($bagianStats as $bagian)
                    <div class="dept-item">
                        <div class="dept-info">
                            <div class="dept-icon {{ $bagian['bg_class'] }}">
                                <i class="{{ $bagian['icon'] }}"></i>
                            </div>
                            <span class="dept-name">{{ $bagian['nama_bagian'] }}</span>
                        </div>
                        <span class="dept-count">{{ $bagian['total_surat'] }}</span>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-muted">Belum ada data bagian</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
        {{-- ANCHOR: Statistics Chart Content "Distribusi Jenis Surat" --}}
        <div class="{{ $isAdmin ? 'col-lg-6' : 'col-lg-12' }}">
            <div class="chart-right">
                <h4 class="section-title">Distribusi Jenis Surat</h4>
                <div class="chart-container">
                    <canvas id="distributionChart"></canvas>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-color bg-success" style="background-color: #66bb6a;"></div>
                        <span>Surat Masuk</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #42a5f5;"></div>
                        <span>Surat Keluar</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ffca28;"></div>
                        <span>Disposisi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- !SECTION: Distribution Statistics Chart --}}

{{-- SECTION: Recent Activity Table --}}
<div class="activity-section">
    {{-- ANCHOR: Recent Activity Table Header Section --}}
    <div class="activity-header-section">
        <div class="activity-title-wrapper">
            <h3 class="activity-title">
                <i class="fas fa-list-ul"></i>
                Aktivitas Surat Terbaru
            </h3>
        </div>
        <div class="activity-controls">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Cari arsip..." id="searchInput" value="{{ request()->get('search', '') }}">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>
    </div>

    {{-- ANCHOR: Recent Activity Table Content --}}
    <div id="activityTableContainer">
        @include('partials.table', [
            'tableId' => 'activityTable',
            'tableClass' => '',
            'thead' => view()->make('pages.dasbor._table_head')->render(),
            'tbody' => view()->make('pages.dasbor._table_body', [
                'recentActivity' => $recentActivity,
                'pagination' => $pagination,
            ])->render(),
        ])
    </div>

    {{-- ANCHOR: Recent Activity Table Footer --}}
    @include('partials.pagination', [
        'currentPage' => $pagination['current_page'],
        'totalPages' => $pagination['total_pages'],
        'baseUrl' => $pagination['base_url'],
        'showInfo' => $pagination['show_info']
    ])
</div>
{{-- !SECTION: Recent Activity Table --}}

{{-- ANCHOR: Detail Modals --}}
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

{{-- !ANCHOR: Detail Modals --}}

@endsection 


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    console.log('=== DASBOR SCRIPT LOADED ===');
    console.log('Script loaded successfully!');
    
    // Global variables
    let distributionChart;
    const chartData = {
        labels: {!! json_encode($chartData['labels']) !!},
        data: {!! json_encode($chartData['data']) !!},
        colors: {!! json_encode($chartData['colors']) !!}
    };
    let bagianStatsCache = {!! json_encode($bagianStats) !!};
    const isAdmin = {!! json_encode($isAdmin) !!};
    const pagination = {!! json_encode($pagination) !!};

    // DOM Content Loaded Event
    document.addEventListener('DOMContentLoaded', function() {
        initializeChart();
        registerEventListeners();
        
        // ANCHOR: Auto scroll to table if search parameter exists
        checkAndScrollToTable();
    });

    // Initialize Chart with softer colors and better sizing
    function initializeChart() {
        const ctx = document.getElementById('distributionChart');
        if (!ctx) return;
        
        distributionChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: chartData.labels,
                datasets: [{
                    data: chartData.data,
                    backgroundColor: chartData.colors,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 8,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#2E2E2E',
                        bodyColor: '#4CAF50',
                        borderColor: '#4CAF50',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} surat (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000,
                    easing: 'easeOutCubic'
                },
                layout: {
                    padding: {
                        top: 10,
                        bottom: 10,
                        left: 10,
                        right: 10
                    }
                }
            }
        });
    }


    // Setup Event Listeners
    function registerEventListeners() {
        console.log('Setting up event listeners...');
        
        // Filter buttons
        const filterButtons = document.querySelectorAll('.filter-btn:not(.dropdown-toggle)');
        console.log('Found filter buttons:', filterButtons.length);
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                updateChart(this.textContent.trim());
            });
        });

        // Year dropdown items
        const yearDropdownItems = document.querySelectorAll('#yearDropdown + .dropdown-menu .dropdown-item');
        console.log('Found year dropdown items:', yearDropdownItems.length);
        yearDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const year = this.getAttribute('data-year');
                const yearDropdown = document.getElementById('yearDropdown');
                if (yearDropdown) {
                    yearDropdown.textContent = year;
                }
                filterButtons.forEach(btn => btn.classList.remove('active'));
                updateChart(year);
            });
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        console.log('Search input element:', searchInput);
        if (searchInput) {
            console.log('Adding change event listener to search input');
            searchInput.addEventListener('change', function() {
                console.log('Search triggered with value:', this.value);
                performSearch(this.value);
            });
        } else {
            console.error('Search input element not found!');
        }


        // Action buttons
        setupActionButtons();
    }

    // Update Chart with Real Data
    async function updateChart(filterType) {
        try {
            let period = '30';
            let year = null;
            
            if (filterType === '7 Hari Terakhir') {
                period = '7';
            } else if (filterType === '30 Hari Terakhir') {
                period = '30';
            } else if (filterType === '90 Hari Terakhir') {
                period = '90';
            } else if (['2024', '2023', '2022', '2021'].includes(filterType)) {
                period = 'year';
                year = filterType;
            }

            const apiUrl = new URL('/api/chart-data', window.location.origin);
            apiUrl.searchParams.set('period', period);
            if (year) {
                apiUrl.searchParams.set('year', year);
            }

            console.log('Fetching chart data from:', apiUrl.toString());

            const response = await fetch(apiUrl.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Failed to fetch chart data');
            }

            if (distributionChart && result.data) {
                distributionChart.data.datasets[0].data = result.data.data;
                distributionChart.update('active');
            }

            if (isAdmin) {
                await refreshBagianStats(period, year);
            }
            
        } catch (error) {
            console.error('Error updating chart:', error);
            showAlert('Error', 'Failed to update chart data: ' + error.message, 'danger');
            
            if (distributionChart) {
                distributionChart.data.datasets[0].data = chartData.data;
                distributionChart.update('active');
            }
        }
    }

    // Fetch Bagian Stats
    async function refreshBagianStats(period, year) {
        // ANCHOR: Fetch bagian stats based on filter selection
        if (!isAdmin) {
            return;
        }

        const deptListElement = document.querySelector('.dept-list');

        if (!deptListElement) {
            return;
        }

        try {
            const apiUrl = new URL('/api/bagian-stats', window.location.origin);
            if (period) {
                apiUrl.searchParams.set('period', period);
            }
            if (year) {
                apiUrl.searchParams.set('year', year);
            }

            const response = await fetch(apiUrl.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to fetch bagian stats');
            }

            bagianStatsCache = Array.isArray(result.data) ? result.data : [];
            renderBagianStats(bagianStatsCache);
        } catch (error) {
            console.error('Error refreshing bagian stats:', error);
            showAlert('Error', 'Failed to update bagian statistics: ' + error.message, 'danger');
        }
    }

    // Render Bagian Stats
    function renderBagianStats(stats) {
        // ANCHOR: Render bagian stats list items in the DOM
        if (!isAdmin) {
            return;
        }

        const deptListElement = document.querySelector('.dept-list');

        if (!deptListElement) {
            return;
        }

        if (!stats || stats.length === 0) {
            deptListElement.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-muted">Belum ada data bagian</p>
                </div>
            `;
            return;
        }

        const listItems = stats.map((bagian) => {
            return `
                <div class="dept-item">
                    <div class="dept-info">
                        <div class="dept-icon ${bagian.bg_class}">
                            <i class="${bagian.icon}"></i>
                        </div>
                        <span class="dept-name">${bagian.nama_bagian}</span>
                    </div>
                    <span class="dept-count">${bagian.total_surat}</span>
                </div>
            `;
        }).join('');

        deptListElement.innerHTML = listItems;
    }


    // Perform Search
    function performSearch(searchTerm) {
        console.log('performSearch called with:', searchTerm);
        const url = new URL(window.location);
        console.log('Current URL:', url.toString());
        
        if (searchTerm.trim()) {
            url.searchParams.set('search', searchTerm);
            console.log('Setting search parameter:', searchTerm);
        } else {
            url.searchParams.delete('search');
            console.log('Removing search parameter');
        }
        url.searchParams.delete('page'); // Reset to first page when searching
        console.log('Final URL:', url.toString());
        
        // Scroll to table when there's a search keyword
        if (searchTerm.trim()) {
            scrollToTable();
        }
        
        window.location.href = url.toString();
    }

    // ANCHOR: Check and Scroll to Table Function
    function checkAndScrollToTable() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        
        if (searchParam && searchParam.trim()) {
            console.log('Search parameter found:', searchParam);
            // Delay scroll to ensure page is fully loaded
            setTimeout(() => {
                scrollToTable();
            }, 300);
        }
    }

    // ANCHOR: Scroll to Table Function
    function scrollToTable() {
        const tableContainer = document.getElementById('activityTableContainer');
        if (tableContainer) {
            tableContainer.scrollIntoView({ 
                block: 'start' 
            });
            console.log('Scrolling to table...');
        }
    }


    const sampleLetterData = {
        1: {
            noSurat: "001/SM/XII/2024",
            tanggalSurat: "15 Desember 2024",
            tanggalTerima: "16 Desember 2024",
            pengirimSurat: "PT. Teknologi Maju Bersama",
            penerimaSurat: "Bagian Sekretariat",
            perihal: "Undangan Rapat Koordinasi Bulanan",
            ringkasanIsi: "Undangan untuk menghadiri rapat koordinasi bulanan yang akan membahas perkembangan proyek teknologi informasi perusahaan. Rapat akan dilaksanakan pada tanggal 20 Desember 2024 di ruang meeting lantai 3.",
            bagian: "Sekretariat",
            jenisSurat: "Surat Masuk",
            disposition: {
                dari: "Kepala Sub Bagian SDM",
                bagianDari: "SDM",
                kepada: "Kepala Bagian SDM", 
                bagianKepada: "SDM",
                sifat: "BIASA",
                tanggalDisposisi: "16/1/2025",
                batasWaktu: "25/1/2025",
                catatan: "Mohon dikaji dan ditindaklanjuti pengajuan kenaikan jabatan untuk 3 karyawan sesuai dengan kriteria dan persyaratan yang telah ditetapkan. Pastikan semua dokumen pendukung lengkap dan sesuai standar perusahaan.",
                statusTindakLanjut: "PROSES"
            }
        },
        2: {
            noSurat: "002/SK/XII/2024",
            tanggalSurat: "14 Desember 2024",
            tanggalKeluar: "14 Desember 2024",
            pengirimSurat: "Bagian Marketing",
            penerimaSurat: "CV. Digital Solusi",
            perihal: "Surat Penawaran Kerjasama Teknologi",
            ringkasanIsi: "Penawaran kerjasama dalam bidang pengembangan sistem informasi dan teknologi digital. Proposal mencakup pengembangan aplikasi mobile dan web untuk meningkatkan efisiensi operasional perusahaan.",
            bagian: "Marketing",
            jenisSurat: "Surat Keluar"
        },
        3: {
            noSurat: "003/SM/XII/2024",
            tanggalSurat: "13 Desember 2024",
            tanggalTerima: "13 Desember 2024",
            pengirimSurat: "Divisi HRD",
            penerimaSurat: "Direktur Utama",
            perihal: "Laporan Kinerja Bulanan Divisi HRD",
            ringkasanIsi: "Laporan mengenai pencapaian target rekrutmen, tingkat kepuasan karyawan, dan program pengembangan SDM yang telah dilaksanakan pada bulan November 2024. Termasuk rekomendasi untuk perbaikan sistem manajemen karyawan.",
            bagian: "HRD",
            jenisSurat: "Surat Masuk",
            disposition: {
                dari: "Direktur Utama",
                bagianDari: "Direksi",
                kepada: "Kepala Bagian HRD", 
                bagianKepada: "HRD",
                sifat: "PENTING",
                tanggalDisposisi: "13/12/2024",
                batasWaktu: "20/12/2024",
                catatan: "Mohon tindak lanjut rekomendasi yang telah disampaikan dalam laporan ini dan berikan feedback mengenai implementasinya.",
                statusTindakLanjut: "SELESAI"
            }
        },
        4: {
            noSurat: "004/SM/XII/2024",
            tanggalSurat: "12 Desember 2024",
            tanggalTerima: "12 Desember 2024",
            pengirimSurat: "Kepala Sub Bagian SDM",
            penerimaSurat: "Kepala Bagian SDM",
            perihal: "Pengajuan Kenaikan Jabatan",
            ringkasanIsi: "Pengajuan kenaikan jabatan untuk 3 karyawan dari berbagai divisi. Memerlukan evaluasi kinerja dan kelengkapan persyaratan administratif.",
            bagian: "SDM",
            jenisSurat: "Surat Masuk",
            disposition: {
                dari: "Kepala Sub Bagian SDM",
                bagianDari: "SDM",
                kepada: "Kepala Bagian SDM", 
                bagianKepada: "SDM",
                sifat: "BIASA",
                tanggalDisposisi: "12/12/2024",
                batasWaktu: "20/12/2024",
                catatan: "Mohon dikaji dan ditindaklanjuti pengajuan kenaikan jabatan untuk 3 karyawan sesuai dengan kriteria dan persyaratan yang telah ditetapkan. Pastikan semua dokumen pendukung lengkap dan sesuai standar perusahaan.",
                statusTindakLanjut: "PROSES"
            }
        },
        5: {
            noSurat: "005/SM/XII/2024",
            tanggalSurat: "11 Desember 2024",
            tanggalTerima: "11 Desember 2024",
            pengirimSurat: "PT. Logistik Terpadu",
            penerimaSurat: "Bagian Pengadaan",
            perihal: "Delivery Order Pengadaan Barang",
            ringkasanIsi: "Pemberitahuan pengiriman barang sesuai dengan purchase order nomor PO/2024/0995. Barang yang dikirim meliputi peralatan komputer, printer, dan supplies kantor dengan total nilai Rp 75.500.000.",
            bagian: "Pengadaan",
            jenisSurat: "Surat Masuk"
        }
    };

    function viewDetail(id) {
        const data = sampleLetterData[id];
        if (!data) return;

        const modalBody = document.getElementById('modalBody');
        const isIncoming = data.jenisSurat === 'Surat Masuk';
        
        let modalContent = `
            <div class="detail-section">
                <div class="section-header">
                    <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Nomor Surat</div>
                        <div class="detail-value">${data.noSurat}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Tanggal Surat</div>
                        <div class="detail-value">${data.tanggalSurat}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">${isIncoming ? 'Tanggal Terima' : 'Tanggal Keluar'}</div>
                        <div class="detail-value">${isIncoming ? data.tanggalTerima : data.tanggalKeluar}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Jenis Surat</div>
                        <div class="detail-value">
                            <span class="status-badge ${isIncoming ? 'status-masuk' : 'status-keluar'}">
                                ${data.jenisSurat}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <div class="section-header">
                    <i class="fas fa-users me-2"></i>Pengirim & Penerima
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Pengirim Surat</div>
                        <div class="detail-value">${data.pengirimSurat}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Penerima Surat</div>
                        <div class="detail-value">${data.penerimaSurat}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Bagian</div>
                        <div class="detail-value">${data.bagian}</div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <div class="section-header">
                    <i class="fas fa-file-alt me-2"></i>Isi Surat
                </div>
                <div class="detail-item">
                    <div class="detail-label">Perihal Surat</div>
                    <div class="detail-value">${data.perihal}</div>
                </div>
                <div class="summary-box">
                    <div class="detail-label mb-2">Ringkasan Isi Surat</div>
                    <p class="summary-text">${data.ringkasanIsi}</p>
                </div>
            </div>
        `;

        // Add disposition section only if data exists and it's incoming mail
        if (data.disposition && isIncoming) {
            modalContent += `
                <div class="detail-section">
                    <div class="section-header">
                        <i class="fas fa-share-alt me-2"></i>Informasi Disposisi
                    </div>
                    <div class="disposition-section">
                        <div class="disposition-info">
                            <div class="detail-item">
                                <div class="detail-label">Disposisi Dari</div>
                                <div class="detail-value">${data.disposition.dari}</div>
                                <div class="detail-label" style="margin-top: 5px;">Bagian: ${data.disposition.bagianDari}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Disposisi Kepada</div>
                                <div class="detail-value">${data.disposition.kepada}</div>
                                <div class="detail-label" style="margin-top: 5px;">Bagian: ${data.disposition.bagianKepada}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Sifat</div>
                                <div class="detail-value">
                                    <span class="status-badge ${data.disposition.sifat === 'PENTING' ? 'status-keluar' : 'status-masuk'}">
                                        ${data.disposition.sifat}
                                    </span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Tanggal Disposisi</div>
                                <div class="detail-value">${data.disposition.tanggalDisposisi}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Batas Waktu</div>
                                <div class="detail-value">${data.disposition.batasWaktu}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Status Tindak Lanjut</div>
                                <div class="detail-value">
                                    <span class="status-badge ${data.disposition.statusTindakLanjut === 'SELESAI' ? 'status-masuk' : 'status-keluar'}">
                                        ${data.disposition.statusTindakLanjut}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="disposition-note">
                            <div class="detail-label">Catatan (Deskripsi)</div>
                            <div class="detail-value">${data.disposition.catatan}</div>
                        </div>
                    </div>
                </div>
            `;
        }

        modalBody.innerHTML = modalContent;
        console.log(document.getElementById('detailModal'));
        document.getElementById('detailModal').classList.add('show');
    }



    // Setup Action Buttons
    function setupActionButtons() {
        const rows = document.querySelectorAll('.activity-row');
        rows.forEach((row, index) => {
            setupRowActionButtons(row, index + 1);
        });

        const exportBtn = document.querySelector('.export-btn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                showAlert('Info', 'Mengekspor data...', 'info');
            });
        }
    }

    // Setup Row Action Buttons
    function setupRowActionButtons(row, id) {
        console.log(row);
        const viewBtn = row.querySelector('.view-btn');
        const deleteBtn = row.querySelector('.delete-btn');

        if (viewBtn) {
            viewBtn.onclick = function() {
                showAlert('Info', 'Fitur detail surat belum tersedia', 'info');
            };
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                const noSurat = row.cells[1].textContent;
                if (confirm(`Apakah Anda yakin ingin menghapus surat ${noSurat}?`)) {
                    row.style.animation = 'fadeOut 0.5s ease-out forwards';
                    setTimeout(() => {
                        row.remove();
                        const noSuratText = noSurat;
                        showAlert('Sukses', 'Surat berhasil dihapus', 'success');
                    }, 500);
                }
            });
        }
    }

    // Show Alert
    function showAlert(title, message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = `
            top: 20px; 
            right: 20px; 
            z-index: 9999; 
            min-width: 300px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.15); 
            border: none; 
            border-radius: 8px;
        `;

        let bgColor, textColor;
        switch(type) {
            case 'success':
                bgColor = '#d4edda';
                textColor = '#155724';
                break;
            case 'info':
                bgColor = '#d1ecf1';
                textColor = '#0c5460';
                break;
            case 'warning':
                bgColor = '#fff3cd';
                textColor = '#856404';
                break;
            case 'danger':
                bgColor = '#f8d7da';
                textColor = '#721c24';
                break;
            default:
                bgColor = '#d4edda';
                textColor = '#155724';
        }

        alertDiv.style.backgroundColor = bgColor;
        alertDiv.style.color = textColor;

        alertDiv.innerHTML = `
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Resize handler
    window.addEventListener('resize', function() {
        if (distributionChart) {
            distributionChart.resize();
        }
    });
    
    console.log('=== DASBOR SCRIPT COMPLETED ===');
    
    // ANCHOR: Detail Modal Functions
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
            const response = await fetch(`/surat-masuk/${suratMasukId}`);
            if (!response.ok) {
                throw new Error('Failed to fetch surat masuk detail');
            }

            const data = await response.json();
            
            // Populate modal with data
            populateSuratMasukModal(data);
            
        } catch (error) {
            console.error('Error loading surat masuk detail:', error);
            showAlert('Error', 'Gagal memuat detail surat masuk', 'danger');
        }
    };

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
            const response = await fetch(`/surat-keluar/${suratKeluarId}`);
            if (!response.ok) {
                throw new Error('Failed to fetch surat keluar detail');
            }

            const data = await response.json();
            
            // Populate modal with data
            populateSuratKeluarModal(data);
            
        } catch (error) {
            console.error('Error loading surat keluar detail:', error);
            showAlert('Error', 'Gagal memuat detail surat keluar', 'danger');
        }
    };


    // ANCHOR: Modal Population Functions
    function populateSuratMasukModal(data) {
        const parentEl = document.getElementById('modalDetailSuratMasuk');
        const suratMasuk = data.suratMasuk || data; // Handle both response structures
        
        // Store current surat masuk ID for action buttons
        window.currentDetailSuratMasukId = suratMasuk.id;
        window.currentDetailSuratMasuk = suratMasuk;
        
        // Basic information
        const fields = {
            'detail-nomor-surat': suratMasuk.nomor_surat || '-',
            'detail-tanggal-surat': suratMasuk.tanggal_surat ? 
                new Date(suratMasuk.tanggal_surat).toLocaleDateString('id-ID') : '-',
            'detail-tanggal-terima': suratMasuk.tanggal_terima ? 
                new Date(suratMasuk.tanggal_terima).toLocaleDateString('id-ID') : '-',
            'detail-perihal': suratMasuk.perihal || '-',
            'detail-pengirim': suratMasuk.pengirim || '-',
            'detail-sifat-surat': suratMasuk.sifat_surat || '-'
        };

        // Update field values using parent element
        Object.entries(fields).forEach(([id, value]) => {
            const element = parentEl.querySelector(`#${id}`);
            if (element) {
                element.textContent = value;
            }
        });

        // Related information
        const bagianElement = parentEl.querySelector('#detail-bagian-tujuan');
        if (bagianElement) {
            bagianElement.textContent = suratMasuk.tujuan_bagian?.nama_bagian || '-';
        }
        
        const userElement = parentEl.querySelector('#detail-user');
        if (userElement) {
            userElement.textContent = suratMasuk.user?.nama || '-';
        }
        
        // Audit information
        const updatedByElement = parentEl.querySelector('#detail-updated-by');
        if (updatedByElement) {
            updatedByElement.textContent = suratMasuk.updater?.nama || '-';
        }
        
        // Timestamps
        const createdAtElement = parentEl.querySelector('#detail-created-at');
        if (createdAtElement) {
            createdAtElement.textContent = suratMasuk.created_at ? 
                new Date(suratMasuk.created_at).toLocaleString('id-ID') : '-';
        }
        
        const updatedAtElement = parentEl.querySelector('#detail-updated-at');
        if (updatedAtElement) {
            updatedAtElement.textContent = suratMasuk.updated_at ? 
                new Date(suratMasuk.updated_at).toLocaleString('id-ID') : '-';
        }

        // Ringkasan isi
        const ringkasanSection = parentEl.querySelector('#detail-ringkasan-section');
        const ringkasanContent = parentEl.querySelector('#detail-ringkasan-isi');
        if (ringkasanSection && ringkasanContent) {
            if (suratMasuk.ringkasan_isi) {
                ringkasanContent.textContent = suratMasuk.ringkasan_isi;
                ringkasanSection.style.display = 'block';
            } else {
                ringkasanSection.style.display = 'none';
            }
        }

        // Keterangan
        const keteranganSection = parentEl.querySelector('#detail-keterangan-section');
        const keteranganContent = parentEl.querySelector('#detail-keterangan');
        if (keteranganSection && keteranganContent) {
            if (suratMasuk.keterangan) {
                keteranganContent.textContent = suratMasuk.keterangan;
                keteranganSection.style.display = 'block';
            } else {
                keteranganSection.style.display = 'none';
            }
        }

        // Lampiran
        populateLampiranDetail(suratMasuk.lampiran || [], parentEl);
        
        // Disposisi
        populateDisposisiDetail(suratMasuk.disposisi || [], parentEl);
    }

    /**
     * ANCHOR: Populate Lampiran Detail
     * Populate the lampiran section with attachment data
     * @param {Array} lampiran - Array of lampiran data
     * @param {HTMLElement} parentEl - Parent element to search within
     */
    function populateLampiranDetail(lampiran, parentEl) {
        const lampiranContent = parentEl.querySelector('#detail-lampiran-content');
        
        if (!lampiranContent) return;
        
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

    /**
     * ANCHOR: Populate Disposisi Detail
     * Populate the disposisi section with disposisi data
     * @param {Array} disposisi - Array of disposisi data
     * @param {HTMLElement} parentEl - Parent element to search within
     */
    function populateDisposisiDetail(disposisi, parentEl) {
        const disposisiContent = parentEl.querySelector('#detail-disposisi-content');
        const disposisiSection = parentEl.querySelector('#detail-disposisi-section');
        
        if (!disposisiContent || !disposisiSection) return;
        
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
            // ANCHOR: Kepala Bagian Pengirim diambil dari bagian yang dituju di surat masuk
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

    /**
     * ANCHOR: Format Date for Display
     * Format date consistently across the application
     * @param {string} dateString - The date string to format
     * @returns {string} Formatted date string
     */
    const formatDateForDisplay = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    /**
     * ANCHOR: Format DateTime for Display
     * Format datetime consistently across the application
     * @param {string} dateString - The datetime string to format
     * @returns {string} Formatted datetime string
     */
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

    /**
     * ANCHOR: Get File Icon Class
     * Get appropriate icon class based on file extension
     * @param {string} fileName - The file name
     * @returns {string} Icon class string
     */
    const getFileIconClass = (fileName) => {
        const ext = fileName.toLowerCase().split('.').pop();
        switch(ext) {
            case 'pdf': return 'fa-file-pdf text-danger';
            case 'doc': case 'docx': return 'fa-file-word text-primary';
            case 'xls': case 'xlsx': return 'fa-file-excel text-success';
            case 'zip': case 'rar': return 'fa-file-archive text-warning';
            case 'jpg': case 'jpeg': case 'png': case 'gif': return 'fa-file-image text-info';
            case 'txt': return 'fa-file-alt text-secondary';
            default: return 'fa-file text-muted';
        }
    };

    /**
     * ANCHOR: Format File Size
     * Format file size in human readable format
     * @param {number} bytes - File size in bytes
     * @returns {string} Formatted file size
     */
    const formatFileSize = (bytes) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    function populateSuratKeluarModal(data) {
        const parentEl = document.getElementById('modalDetailSuratKeluar');
        const suratKeluar = data.suratKeluar || data; // Handle both response structures
        
        // Store current surat keluar ID for action buttons
        window.currentDetailSuratKeluarId = suratKeluar.id;
        window.currentDetailSuratKeluar = suratKeluar;
        
        // Basic information
        const fields = {
            'detail-nomor-surat': suratKeluar.nomor_surat || '-',
            'detail-tanggal-surat': formatDateForDisplay(suratKeluar.tanggal_surat),
            'detail-tanggal-keluar': formatDateForDisplay(suratKeluar.tanggal_keluar),
            'detail-perihal': suratKeluar.perihal || '-',
            'detail-tujuan': suratKeluar.tujuan || '-'
        };

        // Update field values using parent element
        Object.entries(fields).forEach(([id, value]) => {
            const element = parentEl.querySelector(`#${id}`);
            if (element) {
                element.textContent = value;
            }
        });

        // Sifat surat dengan badge
        const sifatSuratElement = parentEl.querySelector('#detail-sifat-surat');
        if (sifatSuratElement) {
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
        }
        
        // Related information
        const bagianElement = parentEl.querySelector('#detail-bagian-pengirim');
        if (bagianElement) {
            bagianElement.textContent = suratKeluar.pengirim_bagian?.nama_bagian || '-';
        }
        
        // Audit information
        const userElement = parentEl.querySelector('#detail-user');
        if (userElement) {
            userElement.textContent = suratKeluar.user?.username || '-';
        }
        
        const createdAtElement = parentEl.querySelector('#detail-created-at');
        if (createdAtElement) {
            createdAtElement.textContent = formatDateTimeForDisplay(suratKeluar.created_at);
        }
        
        const updatedByElement = parentEl.querySelector('#detail-updated-by');
        if (updatedByElement) {
            updatedByElement.textContent = suratKeluar.updater?.username || '-';
        }
        
        const updatedAtElement = parentEl.querySelector('#detail-updated-at');
        if (updatedAtElement) {
            updatedAtElement.textContent = formatDateTimeForDisplay(suratKeluar.updated_at);
        }

        // Ringkasan isi
        const ringkasanSection = parentEl.querySelector('#detail-ringkasan-section');
        const ringkasanContent = parentEl.querySelector('#detail-ringkasan-isi');
        if (ringkasanSection && ringkasanContent) {
            if (suratKeluar.ringkasan_isi) {
                ringkasanContent.textContent = suratKeluar.ringkasan_isi;
                ringkasanSection.style.display = 'block';
            } else {
                ringkasanSection.style.display = 'none';
            }
        }

        // Keterangan
        const keteranganSection = parentEl.querySelector('#detail-keterangan-section');
        const keteranganContent = parentEl.querySelector('#detail-keterangan');
        if (keteranganSection && keteranganContent) {
            if (suratKeluar.keterangan) {
                keteranganContent.textContent = suratKeluar.keterangan;
                keteranganSection.style.display = 'block';
            } else {
                keteranganSection.style.display = 'none';
            }
        }

        // Lampiran
        populateLampiranDetailSuratKeluar(suratKeluar.lampiran || [], parentEl);
    }

    /**
     * ANCHOR: Populate Lampiran Detail for Surat Keluar
     * Populate the lampiran section with attachment data for surat keluar
     * @param {Array} lampiran - Array of lampiran data
     * @param {HTMLElement} parentEl - Parent element to search within
     */
    function populateLampiranDetailSuratKeluar(lampiran, parentEl) {
        const lampiranContent = parentEl.querySelector('#detail-lampiran-content');
        
        if (!lampiranContent) return;
        
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
            const iconClass = getFileIconClass(file.nama_file);
            const downloadUrl = `/storage/${file.path_file}`;
            const fileSize = file.file_size ? formatFileSize(file.file_size) : 'Unknown';
            const badgeClass = file.tipe_lampiran === 'utama' ? 'badge-primary' : 'badge-secondary';
            
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
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge ${badgeClass}">
                                            ${file.tipe_lampiran === 'utama' ? 'Lampiran Utama' : 'Dokumen Pendukung'}
                                        </span>
                                        <small class="text-muted">${fileSize}</small>
                                    </div>
                                    <small class="text-muted">
                                        ${file.created_at ? formatDateTimeForDisplay(file.created_at) : '-'}
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

</script>
@endpush