<form id="editBagianForm" action="" method="POST">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="bagian_id" id="edit_bagian_id" value="">
    
    <div class="mb-3">
        <label for="edit_nama_bagian" class="form-label">Nama Bagian <span class="text-danger">*</span></label>
        <input type="text" name="nama_bagian" class="form-control" id="edit_nama_bagian" placeholder="Nama Bagian" required>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_kepala_bagian" class="form-label">Kepala Bagian</label>
        <input type="text" name="kepala_bagian" class="form-control" id="edit_kepala_bagian" placeholder="Kepala Bagian">
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" id="edit_status" required>
            <option value="">Pilih Status</option>
            <option value="Aktif">Aktif</option>
            <option value="Nonaktif">Tidak Aktif</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_keterangan" class="form-label">Deskripsi</label>
        <textarea name="keterangan" class="form-control" id="edit_keterangan" rows="3" placeholder="Deskripsi bagian"></textarea>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="editBagianCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="editBagianSubmitBtn">Simpan</button>
    </div>
</form>
