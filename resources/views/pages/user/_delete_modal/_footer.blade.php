<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close" id="deleteUserCancelBtn">
    Batal
</button>
<form id="deleteUserForm" method="POST" style="display: inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" id="deleteUserSubmitBtn">
        Ya, Hapus
    </button>
</form>
