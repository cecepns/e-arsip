<form id="deleteBagianForm" method="POST" style="display: inline;">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <button type="submit" class="btn btn-danger" id="deleteBagianSubmitBtn">Ya, Hapus</button>
</form>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="deleteBagianCancelBtn">Batal</button>
