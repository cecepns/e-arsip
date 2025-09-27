<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close" id="deleteSuratMasukCancelBtn">
    Batal
</button>
<form id="deleteSuratMasukForm" method="POST" style="display: inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" id="deleteSuratMasukSubmitBtn">
        Ya, Hapus
    </button>
</form>
