{{-- Delete confirmation modal content --}}
<div class="text-center">
    <div class="mb-3">
        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
    </div>
    <h5 class="mb-3">Konfirmasi Hapus User</h5>
    <p class="mb-3">Apakah Anda yakin ingin menghapus user <strong id="deleteUserName"></strong>?</p>
    <p class="text-muted mb-4">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait user tersebut.</p>
    
    <div class="d-flex justify-content-center gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Batal
        </button>
        <form id="deleteUserForm" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-1"></i>Ya, Hapus
            </button>
        </form>
    </div>
</div>
