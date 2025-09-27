<form id="editUserForm" action="" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="user_id" id="edit_user_id" value="">
    
    <div class="mb-3">
        <label for="edit_username" class="form-label">Username</label>
        <input type="text" name="username" class="form-control" id="edit_username" placeholder="Username" required>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_nama" class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" id="edit_nama" placeholder="Nama Lengkap" required>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" id="edit_email" placeholder="email@example.com" required>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_password" class="form-label">Password</label>
        <input type="text" name="password" class="form-control" id="edit_password" placeholder="Kosongkan jika tidak ingin mengubah password">
        <div class="form-text">Kosongkan jika tidak ingin mengubah password (password lama akan dipertahankan)</div>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_role" class="form-label">Role</label>
        <select name="role" class="form-select" id="edit_role" required>
            <option value="Staf">Staf</option>
            <option value="Admin">Admin</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_bagian_id" class="form-label">Bagian</label>
        <select name="bagian_id" class="form-select" id="edit_bagian_id">
            <option value="">Pilih Bagian</option>
            @foreach($bagian ?? [] as $bagianItem)
                <option value="{{ $bagianItem->id }}">
                    {{ $bagianItem->nama_bagian }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <div class="form-check">
            <input type="checkbox" name="is_kepala_bagian" class="form-check-input" id="edit_is_kepala_bagian" value="1">
            <label for="edit_is_kepala_bagian" class="form-check-label">
                Kepala Bagian
            </label>
        </div>
        <div class="form-text">Centang jika user ini akan menjadi kepala bagian. Pastikan bagian sudah dipilih.</div>
        <div class="invalid-feedback"></div>
    </div>
    
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" aria-label="close" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="editUserSubmitBtn">Update</button>
    </div>
</form>
