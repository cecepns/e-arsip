<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close" id="deleteSuratKeluarCancelBtn">
    Batal
</button>
<form id="deleteSuratKeluarForm" method="POST" style="display: inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" id="deleteSuratKeluarSubmitBtn">
        Ya, Hapus
    </button>
</form>
