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
        <label for="edit_kepala_bagian_user_id" class="form-label">Kepala Bagian</label>
        <select name="kepala_bagian_user_id" class="form-select" id="edit_kepala_bagian_user_id">
            <option value="">Pilih Kepala Bagian (Opsional)</option>
            @foreach(\App\Models\User::whereNotNull('bagian_id')->get() as $user)
                <option value="{{ $user->id }}">
                    {{ $user->username }} ({{ $user->bagian->nama_bagian ?? 'Tanpa Bagian' }})
                </option>
            @endforeach
        </select>
        <div class="form-text">Pilih user yang akan menjadi kepala bagian. Kosongkan jika belum ada user.</div>
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
