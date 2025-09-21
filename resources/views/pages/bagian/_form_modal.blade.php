<form>
    <div class="mb-3">
        <label for="nama_bagian" class="form-label">Nama Bagian</label>
        <input type="text" name="nama_bagian" class="form-control" id="nama_bagian" placeholder="Nama Bagian">
    </div>
    <div class="mb-3">
        <label for="kepala_bagian" class="form-label">Kepala Bagian</label>
        <input type="text" name="kepala_bagian" class="form-control" id="kepala_bagian" placeholder="Kepala Bagian">
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" class="form-select form-select mb-3" id="status">
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="deskripsi" class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" id="deskripsi" rows="3"></textarea>
    </div>
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>