@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid">
    <!-- Page Title -->
    @include('partials.page-title', [
        'title' => 'Notifikasi',
        'subtitle' => 'Kelola dan lihat semua notifikasi Anda'
    ])

    <!-- Page Actions -->
    <div class="d-flex justify-content-end sub-page-header mt-5">
        <button class="btn btn-outline-primary" id="markAllAsRead">
            <i class="fas fa-check-double me-2"></i>
            Tandai Semua Dibaca
        </button>
    </div>

    <!-- Notifications List -->
    <div class="row">
        <div class="col-12">
            @if($notifications->count() > 0)
                @foreach($groupedNotifications as $date => $notificationsGroup)
                    <div class="mb-4">
                        <!-- Date Header -->
                        <div class="mb-3">
                            <div class="badge bg-primary fs-6 px-3 py-2">
                                <i class="fas fa-calendar-day me-2"></i>
                                {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                            </div>
                        </div>
                        
                        <!-- Notifications List -->
                        <div class="row g-3">
                            @foreach($notificationsGroup as $notification)
                                <div class="col-12">
                                    <div class="card {{ !$notification->is_read ? 'border-start border-primary border-2' : '' }} shadow-sm" 
                                         data-notification-id="{{ $notification->id }}">
                                        
                                        <!-- Notification Header -->
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <div>
                                                @switch($notification->type)
                                                    @case('surat_masuk')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-inbox me-1"></i>
                                                            Surat Masuk
                                                        </span>
                                                        @break
                                                    @case('surat_keluar')
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-paper-plane me-1"></i>
                                                            Surat Keluar
                                                        </span>
                                                        @break
                                                    @case('disposisi')
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-share-alt me-1"></i>
                                                            Disposisi
                                                        </span>
                                                        @break
                                                @endswitch
                                            </div>
                                            
                                            <div class="d-flex align-items-center gap-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $notification->created_at->locale('id')->diffForHumans() }}
                                                </small>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-primary rounded-pill">
                                                        <i class="fas fa-circle"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Notification Content -->
                                        <div class="card-body">
                                            <h6 class="card-title fw-bold">{{ $notification->title }}</h6>
                                            <p class="card-text text-muted">{{ $notification->message }}</p>
                                            
                                            @if($notification->data)
                                                <div class="row g-2 mt-3">
                                                    @switch($notification->type)
                                                        @case('surat_masuk')
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                                                    <div>
                                                                        <small class="text-muted d-block">Nomor Surat</small>
                                                                        <span class="fw-medium">{{ $notification->data['nomor_surat'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                    <i class="fas fa-tag text-success me-2"></i>
                                                                    <div>
                                                                        <small class="text-muted d-block">Perihal</small>
                                                                        <span class="fw-medium">{{ $notification->data['perihal'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                    <i class="fas fa-user text-info me-2"></i>
                                                                    <div>
                                                                        <small class="text-muted d-block">Pengirim</small>
                                                                        <span class="fw-medium">{{ $notification->data['pengirim'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @break
                                                        @case('surat_keluar')
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                                                    <div>
                                                                        <small class="text-muted d-block">Nomor Surat</small>
                                                                        <span class="fw-medium">{{ $notification->data['nomor_surat'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                    <i class="fas fa-tag text-success me-2"></i>
                                                                    <div>
                                                                        <small class="text-muted d-block">Perihal</small>
                                                                        <span class="fw-medium">{{ $notification->data['perihal'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                    <i class="fas fa-user text-info me-2"></i>
                                                                    <div>
                                                                        <small class="text-muted d-block">Penerima</small>
                                                                        <span class="fw-medium">{{ $notification->data['penerima'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @break
                                                        @case('disposisi')
                                                            <div class="col-md-3">
                                                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                                                    <div>
                                                                        <small class="text-muted d-block">Nomor Surat</small>
                                                                        <span class="fw-medium">{{ $notification->data['nomor_surat'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                    <i class="fas fa-tag text-success me-2"></i>
                                                                    <div>
                                                                        <small class="text-muted d-block">Perihal</small>
                                                                        <span class="fw-medium">{{ $notification->data['perihal'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                    <i class="fas fa-arrow-right text-warning me-2"></i>
                                                                    <div>
                                                                        <small class="text-muted d-block">Dari Bagian</small>
                                                                        <span class="fw-medium">{{ $notification->data['dari_bagian'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                    <i class="fas fa-arrow-left text-info me-2"></i>
                                                                    <div>
                                                                        <small class="text-muted d-block">Ke Bagian</small>
                                                                        <span class="fw-medium">{{ $notification->data['ke_bagian'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @break
                                                    @endswitch
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Notification Actions -->
                                        @if(!$notification->is_read)
                                            <div class="card-footer bg-light">
                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-sm btn-primary mark-as-read" 
                                                            data-notification-id="{{ $notification->id }}">
                                                        <i class="fas fa-check me-1"></i>
                                                        Tandai Dibaca
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="card shadow-sm">
                        <div class="card-body py-5">
                            <i class="fas fa-bell-slash text-muted mb-4" style="font-size: 4rem;"></i>
                            <p class="card-text text-muted">
                                Anda belum memiliki notifikasi. Notifikasi akan muncul ketika ada aktivitas baru.
                            </p>
                            <a href="{{ route('dasbor.index') }}" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark single notification as read
    document.querySelectorAll('.mark-as-read').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = this.dataset.notificationId;
            markNotificationAsRead(notificationId);
        });
    });

    // Mark all notifications as read
    document.getElementById('markAllAsRead')?.addEventListener('click', function() {
        markAllNotificationsAsRead();
    });

    // Mark notification as read when clicked
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking on buttons or footer
            if (e.target.closest('.card-footer') || 
                e.target.closest('.mark-as-read')) return;
            
            const notificationId = this.dataset.notificationId;
            if (notificationId && this.classList.contains('border-start')) {
                markNotificationAsRead(notificationId);
            }
        });
    });
});

function markNotificationAsRead(notificationId) {
    const notificationCard = document.querySelector(`[data-notification-id="${notificationId}"]`);
    if (!notificationCard) return;

    // Show loading state
    const button = notificationCard.querySelector('.mark-as-read');
    if (button) {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
        button.disabled = true;
    }

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
            // Remove unread styling
            notificationCard.classList.remove('border-start', 'border-primary', 'border-4');
            
            // Remove footer with button
            const footer = notificationCard.querySelector('.card-footer');
            if (footer) {
                footer.remove();
            }
            
            // Remove unread indicator
            const unreadIndicator = notificationCard.querySelector('.badge.bg-primary.rounded-pill');
            if (unreadIndicator) {
                unreadIndicator.remove();
            }
            
            // Show success message
            showToast('Notifikasi berhasil ditandai sebagai dibaca', 'success');
        } else {
            showToast('Gagal menandai notifikasi sebagai dibaca', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat memproses notifikasi', 'error');
        
        // Restore button state
        if (button) {
            button.innerHTML = '<i class="fas fa-check me-1"></i>Tandai Dibaca';
            button.disabled = false;
        }
    });
}

function markAllNotificationsAsRead() {
    const button = document.getElementById('markAllAsRead');
    if (!button) return;

    // Show loading state
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
    button.disabled = true;

    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove unread class from all notifications
            document.querySelectorAll('.card.border-start').forEach(card => {
                card.classList.remove('border-start', 'border-primary', 'border-4');
                
                // Remove footer with button
                const footer = card.querySelector('.card-footer');
                if (footer) {
                    footer.remove();
                }
                
                // Remove unread indicator
                const unreadIndicator = card.querySelector('.badge.bg-primary.rounded-pill');
                if (unreadIndicator) {
                    unreadIndicator.remove();
                }
            });
            
            showToast('Semua notifikasi berhasil ditandai sebagai dibaca', 'success');
        } else {
            showToast('Gagal menandai semua notifikasi sebagai dibaca', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat memproses notifikasi', 'error');
    })
    .finally(() => {
        // Restore button state
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function showToast(message, type = 'info') {
    if (typeof Toastify !== 'undefined') {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            className: `toastify-${type}`,
            stopOnFocus: true
        }).showToast();
    }
}
</script>
@endpush
