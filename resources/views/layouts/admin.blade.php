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
        <!-- Dashboard - Available for all roles -->
        <div class="nav-item">
            <a href="{{ route('dasbor.index') }}" class="nav-link{{ request()->routeIs('dasbor.index') ? ' active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <!-- Surat Masuk - Available for all roles -->
        <div class="nav-item">
            <a href="{{ route('surat_masuk.index') }}" class="nav-link{{ request()->routeIs('surat_masuk.index') ? ' active' : '' }}">
                <i class="fas fa-inbox"></i>
                <span>Surat Masuk</span>
            </a>
        </div>
        
        <!-- Surat Keluar - Available for all roles -->
        <div class="nav-item">
            <a href="{{ route('surat_keluar.index') }}" class="nav-link{{ request()->routeIs('surat_keluar.index') ? ' active' : '' }}">
                <i class="fas fa-paper-plane"></i>
                <span>Surat Keluar</span>
            </a>
        </div>
        
        <!-- Data Bagian - Admin only -->
        @if(Auth::user()->role === 'Admin')
        <div class="nav-item">
            <a href="{{ route('bagian.index') }}" class="nav-link{{ request()->routeIs('bagian.index') ? ' active' : '' }}">
                <i class="fas fa-building"></i>
                <span>Data Bagian</span>
            </a>
        </div>
        @endif
        
        <!-- Data User - Admin only -->
        @if(Auth::user()->role === 'Admin')
        <div class="nav-item">
            <a href="{{ route('user.index') }}" class="nav-link{{ request()->routeIs('user.index') ? ' active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Data User</span>
            </a>
        </div>
        @endif
        
        <!-- Disposisi - Available for all roles -->
        <div class="nav-item">
            <a href="{{ route('disposisi.index') }}" class="nav-link{{ request()->routeIs('disposisi.*') ? ' active' : '' }}">
                <i class="fas fa-share-alt"></i>
                <span>Disposisi</span>
            </a>
        </div>
        
        <!-- Laporan - Available for all roles -->
        <div class="nav-item">
            <a href="{{ route('laporan.index') }}" class="nav-link{{ request()->routeIs('laporan.*') ? ' active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan</span>
            </a>
        </div>
        
        <!-- Notifikasi - Available for all roles -->
        <div class="nav-item">
            <a href="{{ route('notifications.index') }}" class="nav-link{{ request()->routeIs('notifications.*') ? ' active' : '' }}">
                <i class="fas fa-bell"></i>
                <span>Notifikasi</span>
            </a>
        </div>
        
        <!-- Pengaturan - Admin only -->
        @if(Auth::user()->role === 'Admin')
        <div class="nav-item">
            <a href="{{ route('settings.index') }}" class="nav-link{{ request()->routeIs('settings.*') ? ' active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
        </div>
        @endif
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
                <span class="notification-badge" id="notificationBadge">0</span>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <span class="notification-header-title">Notifikasi Terbaru</span>
                        <button class="notification-close" id="notificationClose">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <div class="notification-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Memuat notifikasi...</span>
                        </div>
                    </div>
                    <div class="notification-footer">
                        <a href="{{ route('notifications.index') }}" class="notification-view-all">Lihat Semua Notifikasi</a>
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

@push('styles')
<style>
.notification-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 2rem;
    color: #6c757d;
}

.notification-loading i {
    font-size: 1.2rem;
}

.notification-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 2rem;
    color: #6c757d;
}

.notification-empty i {
    font-size: 2rem;
    color: #dee2e6;
}

.notification-item {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.notification-badge:empty {
    display: none;
}
</style>
@endpush

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
        const notificationList = document.getElementById('notificationList');
        const notificationBadge = document.getElementById('notificationBadge');

        if (!notificationBell || !notificationDropdown) return;

        // Load notifications on page load
        loadNotifications();

        // Toggle notification dropdown
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
            
            // Load notifications when dropdown is opened
            if (notificationDropdown.classList.contains('show')) {
                loadNotifications();
            }
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

        // Load notifications every 30 seconds
        setInterval(loadNotifications, 30000);
    }

    // Load notifications from server
    function loadNotifications() {
        fetch('/notifications/unread')
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.unread_count);
                updateNotificationList(data.notifications);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
    }

    // Update notification badge
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'block' : 'none';
        }
    }

    // Update notification list
    function updateNotificationList(notifications) {
        const notificationList = document.getElementById('notificationList');
        if (!notificationList) return;

        if (notifications.length === 0) {
            return;
        }

        const notificationsHtml = notifications.map(notification => {
            const iconClass = getNotificationIcon(notification.type);
            const timeAgo = getTimeAgo(notification.created_at);
            
            return `
                <div class="notification-item" data-notification-id="${notification.id}">
                    <div class="notification-icon ${notification.type}">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-desc">${notification.message}</div>
                        <div class="notification-time">${timeAgo}</div>
                    </div>
                </div>
            `;
        }).join('');

        notificationList.innerHTML = notificationsHtml;

        // Add click handlers for notifications
        notificationList.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const notificationId = this.dataset.notificationId;
                markNotificationAsRead(notificationId);
            });
        });
    }

    // Get notification icon based on type
    function getNotificationIcon(type) {
        switch(type) {
            case 'surat_masuk':
                return 'fas fa-inbox';
            case 'surat_keluar':
                return 'fas fa-paper-plane';
            case 'disposisi':
                return 'fas fa-share-alt';
            default:
                return 'fas fa-bell';
        }
    }

    // Get time ago string
    function getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) {
            return 'Baru saja';
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return `${minutes} menit yang lalu`;
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return `${hours} jam yang lalu`;
        } else {
            const days = Math.floor(diffInSeconds / 86400);
            return `${days} hari yang lalu`;
        }
    }

    // Mark notification as read
    function markNotificationAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the notification from the list
                const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.remove();
                }
                
                // Reload notifications to update badge
                loadNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
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
