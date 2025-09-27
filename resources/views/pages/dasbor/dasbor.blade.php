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
                <div class="stat-change {{ $stat['change_type'] }}">
                    <i class="fas fa-arrow-{{ $stat['change_type'] == 'positive' ? 'up' : 'down' }}"></i>
                    <span>{{ $stat['change_text'] }}</span>
                </div>
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
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-year="2024">2024</a></li>
                    <li><a class="dropdown-item" href="#" data-year="2023">2023</a></li>
                    <li><a class="dropdown-item" href="#" data-year="2022">2022</a></li>
                    <li><a class="dropdown-item" href="#" data-year="2021">2021</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- ANCHOR: Statistics Chart Content "Surat per Bagian" --}}
        <div class="col-lg-6">
            <div class="chart-left">
                <h4 class="section-title">Statistik Surat per Bagian</h4>
                <div class="dept-list">
                    <div class="dept-item">
                        <div class="dept-info">
                            <div class="dept-icon bg-success">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="dept-name">Sumber Daya Manusia</span>
                        </div>
                        <span class="dept-count">45</span>
                    </div>
                    <div class="dept-item">
                        <div class="dept-info">
                            <div class="dept-icon bg-primary">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <span class="dept-name">Keuangan</span>
                        </div>
                        <span class="dept-count">38</span>
                    </div>
                    <div class="dept-item">
                        <div class="dept-info">
                            <div class="dept-icon bg-warning">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <span class="dept-name">Pengadaan</span>
                        </div>
                        <span class="dept-count">32</span>
                    </div>
                    <div class="dept-item">
                        <div class="dept-info">
                            <div class="dept-icon bg-danger">
                                <i class="fas fa-building"></i>
                            </div>
                            <span class="dept-name">Sekretariat</span>
                        </div>
                        <span class="dept-count">28</span>
                    </div>
                    <div class="dept-item">
                        <div class="dept-info">
                            <div class="dept-icon bg-success">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <span class="dept-name">Teknologi Informasi</span>
                        </div>
                        <span class="dept-count">25</span>
                    </div>
                </div>
            </div>
        </div>
        {{-- ANCHOR: Statistics Chart Content "Distribusi Jenis Surat" --}}
        <div class="col-lg-6">
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
            <div class="show-entries">
                <label>Tampilkan:</label>
                <select class="entries-select" id="entriesSelect">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Cari arsip..." id="searchInput">
                <i class="fas fa-search search-icon"></i>
            </div>
            <button class="export-btn">
                <i class="fas fa-download"></i>
                Export
            </button>
        </div>
    </div>

    {{-- ANCHOR: Recent Activity Table Content --}}
    @include('partials.table', [
        'tableId' => 'activityTable',
        'tableClass' => 'activity-table',
        'thead' => view()->make('pages.dasbor._table_head')->render(),
        'tbody' => view()->make('pages.dasbor._table_body', ['recentActivity' => $recentActivity])->render(),
    ])

    {{-- ANCHOR: Recent Activity Table Footer --}}
    @include('partials.pagination', [
        'currentPage' => 1,
        'totalPages' => 3,
        'baseUrl' => '#',
        'showInfo' => 'Menampilkan 1-5 dari 100 entries'
    ])
</div>
{{-- !SECTION: Recent Activity Table --}}
@endsection

{{-- ANCHOR: Detail Modal --}}
@include('partials.modal', [
    'id' => 'detailModal',
    'title' => 'Modal 1',
    'body' => 'Show a second modal and hide this one with the button below.',
    'footer' => '<button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">Open second modal</button>'
])
{{-- !ANCHOR: Detail Modal --}}

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Global variables
    let distributionChart;
    const chartData = {
        labels: {!! json_encode($chartData['labels']) !!},
        data: {!! json_encode($chartData['data']) !!},
        colors: {!! json_encode($chartData['colors']) !!}
    };
    let originalTableData = [];

    // DOM Content Loaded Event
    document.addEventListener('DOMContentLoaded', function() {
        initializeChart();
        setupEventListeners();
        initializeTableData();
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

    // Initialize Table Data
    function initializeTableData() {
        const tableBody = document.getElementById('activityTableBody');
        if (!tableBody) return;
        
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            const cells = row.querySelectorAll('td');
            originalTableData.push({
                no: cells[0].textContent,
                noSurat: cells[1].textContent,
                tanggal: cells[2].textContent,
                perihal: cells[3].textContent,
                jenis: cells[4].textContent,
                element: row.cloneNode(true)
            });
        });
    }

    // Setup Event Listeners
    function setupEventListeners() {
        // Filter buttons
        const filterButtons = document.querySelectorAll('.filter-btn:not(.dropdown-toggle)');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                updateChart(this.textContent.trim());
            });
        });

        // Year dropdown items
        const yearDropdownItems = document.querySelectorAll('#yearDropdown + .dropdown-menu .dropdown-item');
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
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterTable(this.value);
            });
        }

        // Entries select
        const entriesSelect = document.getElementById('entriesSelect');
        if (entriesSelect) {
            entriesSelect.addEventListener('change', function() {
                updateTableEntries(this.value);
            });
        }

        // Action buttons
        setupActionButtons();
    }

    // Update Chart
    function updateChart(filterType) {
        let multiplier = 1;
        if (filterType === '7 Hari Terakhir') {
            multiplier = 0.2;
        } else if (filterType === '30 Hari Terakhir') {
            multiplier = 1;
        } else if (filterType === '90 Hari Terakhir') {
            multiplier = 2.5;
        } else if (['2024', '2023', '2022', '2021'].includes(filterType)) {
            multiplier = 10;
        } else {
            multiplier = 1;
        }

        if (distributionChart) {
            const newData = chartData.data.map(value => Math.round(value * multiplier));
            distributionChart.data.datasets[0].data = newData;
            distributionChart.update('active');
            
            updateDepartmentCounts();
        }
    }

    // Update Department Counts
    function updateDepartmentCounts() {
        const deptData = [45, 38, 32, 28, 25];
        const deptCounts = document.querySelectorAll('.dept-count');
        deptCounts.forEach((count, index) => {
            if (deptData[index] !== undefined) {
                count.textContent = deptData[index];
            }
        });
    }

    // Filter Table
    function filterTable(searchTerm) {
        const tableBody = document.getElementById('activityTableBody');
        if (!tableBody) return;
        
        const searchLower = searchTerm.toLowerCase();
        tableBody.innerHTML = '';

        const filteredData = originalTableData.filter(item => {
            return item.noSurat.toLowerCase().includes(searchLower) ||
                    item.perihal.toLowerCase().includes(searchLower) ||
                    item.jenis.toLowerCase().includes(searchLower);
        });

        filteredData.forEach((item, index) => {
            const row = item.element.cloneNode(true);
            row.cells[0].textContent = index + 1;
            setupRowActionButtons(row, index + 1);
            tableBody.appendChild(row);
        });

        updateShowingInfo(filteredData.length);
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


    // Update Table Entries
    function updateTableEntries(count) {
        const tableBody = document.getElementById('activityTableBody');
        const searchInput = document.getElementById('searchInput');
        if (!tableBody || !searchInput) return;
        
        const currentSearch = searchInput.value.toLowerCase();
        
        let dataToShow = originalTableData;
        if (currentSearch) {
            dataToShow = originalTableData.filter(item => {
                return item.noSurat.toLowerCase().includes(currentSearch) ||
                        item.perihal.toLowerCase().includes(currentSearch) ||
                        item.jenis.toLowerCase().includes(currentSearch);
            });
        }

        tableBody.innerHTML = '';

        const limitedData = dataToShow.slice(0, parseInt(count));
        limitedData.forEach((item, index) => {
            const row = item.element.cloneNode(true);
            row.cells[0].textContent = index + 1;
            setupRowActionButtons(row, index + 1);
            tableBody.appendChild(row);
        });

        updateShowingInfo(limitedData.length, dataToShow.length);
    }

    // Update Showing Info
    function updateShowingInfo(showing, total = originalTableData.length) {
        const showingInfo = document.querySelector('.showing-info');
        if (showingInfo) {
            showingInfo.textContent = `Menampilkan 1-${showing} dari ${total} entries`;
        }
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
                        originalTableData = originalTableData.filter(item => 
                            item.noSurat !== noSuratText
                        );
                        showAlert('Sukses', 'Surat berhasil dihapus', 'success');
                        updateShowingInfo(document.querySelectorAll('.activity-row').length);
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
</script>
@endpush