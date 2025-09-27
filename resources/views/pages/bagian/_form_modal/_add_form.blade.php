<form id="addBagianForm" action="{{ route('bagian.store') }}" method="POST">
    @csrf
    <input type="hidden" name="_method" value="POST">
    
    <div class="mb-3">
        <label for="add_nama_bagian" class="form-label">Nama Bagian <span class="text-danger">*</span></label>
        <input type="text" name="nama_bagian" class="form-control" id="add_nama_bagian" placeholder="Nama Bagian" value="{{ old('nama_bagian') }}" required>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_kepala_bagian_user_id" class="form-label">Kepala Bagian</label>
        <select name="kepala_bagian_user_id" class="form-select" id="add_kepala_bagian_user_id">
            <option value="">Pilih Kepala Bagian (Opsional)</option>
            @foreach(\App\Models\User::whereNotNull('bagian_id')->get() as $user)
                <option value="{{ $user->id }}" {{ old('kepala_bagian_user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->username }} ({{ $user->bagian->nama_bagian ?? 'Tanpa Bagian' }})
                </option>
            @endforeach
        </select>
        <div class="form-text">Pilih user yang akan menjadi kepala bagian. Kosongkan jika belum ada user.</div>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" id="add_status" required>
            <option value="">Pilih Status</option>
            <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_keterangan" class="form-label">Deskripsi</label>
        <textarea name="keterangan" class="form-control" id="add_keterangan" rows="3" placeholder="Deskripsi bagian">{{ old('keterangan') }}</textarea>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="addBagianCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="addBagianSubmitBtn">Simpan</button>
    </div>
</form>
