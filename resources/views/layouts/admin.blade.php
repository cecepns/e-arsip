@extends('layouts.base')

@section('title', 'Login | Sistem Dokumen')

@push('head')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            <i class="fas fa-file-alt"></i>
            <span>E-Arsip</span>
        </a>
    </div>
    
    <div class="user-profile">
        <div class="user-avatar">
            <img src="https://placehold.co/600x400?text=Admin" alt="Admin" class="rounded-circle">
        </div>
        <div class="user-info">
            <h6>Admin</h6>
            <p>Administrator</p>
        </div>
    </div>
    
    <div class="sidebar-nav">
        <div class="nav-item">
            <a href="#" class="nav-link active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-inbox"></i>
                <span>Surat Masuk</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-paper-plane"></i>
                <span>Surat Keluar</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-building"></i>
                <span>Data Bagian</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Data User</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-share-alt"></i>
                <span>Disposisi</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
        </div>
    </div>
</nav>

<div class="main-content" id="mainContent">
    <!-- Navbar -->
    <nav class="navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="control-btn" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-brand">
                <h5>Selamat datang, <span class="admin-name">Admin</span></h5>
            </div>
        </div>
        <div class="navbar-controls">
            <div class="notification-bell control-btn" id="notificationBell">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">5</span>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        Notifikasi Terbaru
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon incoming">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">Surat Masuk Baru</div>
                            <div class="notification-desc">Undangan Rapat dari Direktur</div>
                            <div class="notification-time">5 menit yang lalu</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon outgoing">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">Surat Keluar Terkirim</div>
                            <div class="notification-desc">Balasan proposal ke vendor</div>
                            <div class="notification-time">1 jam yang lalu</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon disposition">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">Disposisi Baru</div>
                            <div class="notification-desc">Surat disposisi ke bagian keuangan</div>
                            <div class="notification-time">2 jam yang lalu</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon incoming">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">Surat Masuk Baru</div>
                            <div class="notification-desc">Laporan bulanan dari HRD</div>
                            <div class="notification-time">3 jam yang lalu</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon outgoing">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">Surat Keluar Dikirim</div>
                            <div class="notification-desc">Pengumuman libur nasional</div>
                            <div class="notification-time">5 jam yang lalu</div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="control-btn" id="darkModeToggle">
                <i class="fas fa-moon"></i>
            </button>
            <div class="dropdown user-dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Admin
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">
        <div class="page-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
            <div class="page-title">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>Dashboard</h2>
                    </div>
                    <div>
                        <p class="page-subtitle mb-0">Kelola surat masuk dan surat keluar dengan mudah.</p>
                    </div>
                </div>
            </div>
        </div>

        @yield('admin-content')
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // DOM Content Loaded Event
    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();
        setupDarkMode();
        setupNotifications();
    });

    // Setup Notifications
    function setupNotifications() {
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if (!notificationBell || !notificationDropdown) return;

        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
        });

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });
    }

    // Setup Event Listeners
    function setupEventListeners() {
        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        if (sidebarToggle && sidebar && mainContent) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('sidebar-collapsed');
            });
        }

        // Mobile sidebar
        setupMobileSidebar();
    }

    // Setup Mobile Sidebar
    function setupMobileSidebar() {
        if (window.innerWidth <= 768) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (!sidebar || !sidebarToggle) return;

            document.addEventListener('click', function(e) {
                if (!sidebar.contains(e.target) && 
                    !e.target.matches('#sidebarToggle') && 
                    !e.target.closest('#sidebarToggle')) {
                    sidebar.classList.remove('show');
                }
            });

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
    }

    // Setup Dark Mode
    function setupDarkMode() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;

        if (!darkModeToggle) return;

        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        if (isDarkMode) {
            body.classList.add('dark-mode');
            updateDarkModeIcon(true);
        }

        darkModeToggle.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            const isNowDarkMode = body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isNowDarkMode);
            updateDarkModeIcon(isNowDarkMode);
        });
    }

    // Update Dark Mode Icon
    function updateDarkModeIcon(isDarkMode) {
        const icon = document.querySelector('#darkModeToggle i');
        if (!icon) return;
        icon.className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
    }

    // Resize handler
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) sidebar.classList.remove('show');
        }
    });
</script>
@endpush
