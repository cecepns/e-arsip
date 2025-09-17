@extends('layouts.admin')

@push('head')
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endpush

@section('admin-content')
<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="card-icon-wrapper bg-success">
                <i class="fas fa-inbox"></i>
            </div>
            <h6 class="card-title">Surat Masuk</h6>
            <div class="stat-number">150</div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>12.5% dari bulan lalu</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="card-icon-wrapper bg-info">
                <i class="fas fa-paper-plane"></i>
            </div>
            <h6 class="card-title">Surat Keluar</h6>
            <div class="stat-number">89</div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>8.2% dari bulan lalu</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="card-icon-wrapper bg-warning">
                <i class="fas fa-share-alt"></i>
            </div>
            <h6 class="card-title">Disposisi</h6>
            <div class="stat-number">42</div>
            <div class="stat-change negative">
                <i class="fas fa-arrow-down"></i>
                <span>3.1% dari bulan lalu</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="card-icon-wrapper bg-pink">
                <i class="fas fa-users"></i>
            </div>
            <h6 class="card-title">Total User</h6>
            <div class="stat-number">25</div>
            <div class="stat-change positive">
                <i class="fas fa-plus"></i>
                <span>2 user baru</span>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="chart-section">
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
                        <div class="dept-progress">
                            <div class="progress-bar bg-success" style="width: 90%;"></div>
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
                        <div class="dept-progress">
                            <div class="progress-bar bg-primary" style="width: 76%;"></div>
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
                        <div class="dept-progress">
                            <div class="progress-bar bg-warning" style="width: 64%;"></div>
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
                        <div class="dept-progress">
                            <div class="progress-bar bg-danger" style="width: 56%;"></div>
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
                        <div class="dept-progress">
                            <div class="progress-bar bg-success" style="width: 50%;"></div>
                        </div>
                        <span class="dept-count">25</span>
                    </div>
                </div>
            </div>
        </div>
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

<!-- Recent Activity Section -->
<div class="activity-section">
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
    <div class="table-responsive">
        <table class="activity-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Surat</th>
                    <th>Tanggal Surat</th>
                    <th>Perihal</th>
                    <th>Jenis Surat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="activityTableBody">
                <tr class="activity-row">
                    <td>1</td>
                    <td><strong>001/SM/XII/2024</strong></td>
                    <td>15 Desember 2024</td>
                    <td>Undangan Rapat Koordinasi Bulanan</td>
                    <td><span class="badge-incoming">Surat Masuk</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat" onclick="viewDetail(1)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="activity-row">
                    <td>2</td>
                    <td><strong>002/SK/XII/2024</strong></td>
                    <td>14 Desember 2024</td>
                    <td>Surat Penawaran Kerjasama Teknologi</td>
                    <td><span class="badge-outgoing">Surat Keluar</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat" onclick="viewDetail(2)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="activity-row">
                    <td>3</td>
                    <td><strong>003/SM/XII/2024</strong></td>
                    <td>13 Desember 2024</td>
                    <td>Laporan Kinerja Bulanan Divisi HRD</td>
                    <td><span class="badge-incoming">Surat Masuk</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat" onclick="viewDetail(3)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="activity-row">
                    <td>4</td>
                    <td><strong>004/SM/XII/2024</strong></td>
                    <td>12 Desember 2024</td>
                    <td>Pengajuan Kenaikan Jabatan</td>
                    <td><span class="badge-incoming">Surat Masuk</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat" onclick="viewDetail(4)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="activity-row">
                    <td>5</td>
                    <td><strong>005/SM/XII/2024</strong></td>
                    <td>11 Desember 2024</td>
                    <td>Delivery Order Pengadaan Barang</td>
                    <td><span class="badge-incoming">Surat Masuk</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view-btn" title="Lihat" onclick="viewDetail(5)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        <div class="showing-info">
            <span>Menampilkan 1-5 dari 100 entries</span>
        </div>
        <div class="pagination-wrapper">
            <nav>
                <ul class="pagination">
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Global variables
    let distributionChart;
    const chartData = {
        labels: ['Surat Masuk', 'Surat Keluar', 'Disposisi'],
        data: [150, 89, 42],
        colors: [
            '#66bb6a', // Surat Masuk - Soft Green
            '#42a5f5', // Surat Keluar - Soft Blue  
            '#ffca28', // Disposisi - Soft Yellow
        ]
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