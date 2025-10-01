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
            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->nama }}" class="rounded-circle">
        </div>
        <div class="user-info">
            <h6>{{ Auth::user()->nama }}</h6>
            <p>{{ Auth::user()->role }}</p>
        </div>
    </div>
    
    <div class="sidebar-nav">
        <div class="nav-item">
            <a href="{{ route('dasbor.index') }}" class="nav-link{{ request()->routeIs('dasbor.index') ? ' active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('surat_masuk.index') }}" class="nav-link{{ request()->routeIs('surat_masuk.index') ? ' active' : '' }}">
                <i class="fas fa-inbox"></i>
                <span>Surat Masuk</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('surat_keluar.index') }}" class="nav-link{{ request()->routeIs('surat_keluar.index') ? ' active' : '' }}">
                <i class="fas fa-paper-plane"></i>
                <span>Surat Keluar</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('bagian.index') }}" class="nav-link{{ request()->routeIs('bagian.index') ? ' active' : '' }}">
                <i class="fas fa-building"></i>
                <span>Data Bagian</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('user.index') }}" class="nav-link{{ request()->routeIs('user.index') ? ' active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Data User</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('disposisi.index') }}" class="nav-link{{ request()->routeIs('disposisi.*') ? ' active' : '' }}">
                <i class="fas fa-share-alt"></i>
                <span>Disposisi</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('laporan.index') }}" class="nav-link{{ request()->routeIs('laporan.*') ? ' active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('settings.index') }}" class="nav-link{{ request()->routeIs('settings.*') ? ' active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
        </div>
    </div>
</nav>

<div class="main-content" id="mainContent">
    <!-- Navbar -->
    <nav class="navbar">
        <!-- Left Section: Sidebar Toggle + Brand -->
        <div class="navbar-left">
            <button class="control-btn" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-brand">
                <h5 class="navbar-title">
                    <span class="navbar-title-desktop">Selamat datang, <span class="admin-name">{{ Auth::user()->nama }}</span></span>
                </h5>
            </div>
        </div>
        
        <!-- Right Section: Controls -->
        <div class="navbar-controls">
            <!-- Notification Bell -->
            <div class="notification-bell control-btn" id="notificationBell">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">5</span>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <span class="notification-header-title">Notifikasi Terbaru</span>
                        <button class="notification-close" id="notificationClose">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="notification-list">
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
                    <div class="notification-footer">
                        <a href="#" class="notification-view-all">Lihat Semua Notifikasi</a>
                    </div>
                </div>
            </div>
            
            <!-- Dark Mode Toggle -->
            <button class="control-btn" id="darkModeToggle" title="Toggle Dark Mode">
                <i class="fas fa-moon"></i>
            </button>
            
            <!-- User Dropdown -->
            <div class="dropdown user-dropdown">
                <button class="btn" type="button" data-bs-toggle="dropdown">
                    <img 
                        src="{{ Auth::user()->avatar_url }}" 
                        alt="{{ Auth::user()->nama }}" 
                        class="rounded-circle me-2" 
                        width="32" 
                        height="32"
                        style="object-fit: cover;"
                    >
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">
        @yield('admin-content')
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
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
        const notificationClose = document.getElementById('notificationClose');

        if (!notificationBell || !notificationDropdown) return;

        // Toggle notification dropdown
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
        });

        // Close notification dropdown with close button
        if (notificationClose) {
            notificationClose.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.remove('show');
            });
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });

        // Close notification dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && notificationDropdown.classList.contains('show')) {
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

        // ANCHOR: Sidebar toggle only for desktop screens
        if (sidebarToggle && sidebar && mainContent) {
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('sidebar-collapsed');
                }
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
