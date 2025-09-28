<p>Apakah Anda yakin ingin menghapus disposisi untuk surat <strong id="deleteDisposisiNomor"></strong>?</p>
<p class="text-muted">Data disposisi akan dihapus secara permanen dan tidak dapat dikembalikan.</p>

<div class="d-flex justify-content-end mt-3">
    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="deleteDisposisiCancelBtn">Batal</button>
    <form id="deleteDisposisiForm" method="POST" style="display: inline;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="disposisi_id" id="deleteDisposisiId">
        <button type="submit" class="btn btn-danger" id="deleteDisposisiSubmitBtn">Ya, Hapus</button>
    </form>
</div>
