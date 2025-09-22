<form id="bagianForm" action="{{ route('bagian.store') }}" method="POST">
    @csrf
    <input type="hidden" name="_method" id="formMethod" value="POST">
    <input type="hidden" name="bagian_id" id="bagian_id" value="">
    
    <div class="mb-3">
        <label for="nama_bagian" class="form-label">Nama Bagian</label>
        <input type="text" name="nama_bagian" class="form-control" id="nama_bagian" placeholder="Nama Bagian" value="{{ old('nama_bagian') }}">
    </div>
    <div class="mb-3">
        <label for="kepala_bagian" class="form-label">Kepala Bagian</label>
        <input type="text" name="kepala_bagian" class="form-control" id="kepala_bagian" placeholder="Kepala Bagian" value="{{ old('kepala_bagian') }}">
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" class="form-select form-select mb-3" id="status">
            <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="keterangan" class="form-label">Deskripsi</label>
        <textarea name="keterangan" class="form-control" id="keterangan" rows="3">{{ old('keterangan') }}</textarea>
    </div>
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
    </div>
</form>