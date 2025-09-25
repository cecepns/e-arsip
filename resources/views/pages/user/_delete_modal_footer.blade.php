<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">
    Batal
</button>
<form id="deleteUserForm" method="POST" style="display: inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">
        Ya, Hapus
    </button>
</form>
