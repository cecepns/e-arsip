<form id="addUserForm" action="{{ route('user.store') }}" method="POST">
    @csrf
    
    
    <div class="mb-3">
        <label for="add_username" class="form-label">Username</label>
        <input type="text" name="username" class="form-control" id="add_username" placeholder="Username" value="{{ old('username') }}">
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" id="add_email" placeholder="email@example.com" value="{{ old('email') }}">
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_password" class="form-label">Password</label>
        <input type="text" name="password" class="form-control" id="add_password" placeholder="Password" value="{{ old('password') }}">
        <div class="form-text">Password akan disimpan dalam bentuk plain text</div>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_role" class="form-label">Role</label>
        <select name="role" class="form-select" id="add_role" required>
            <option value="Staf" {{ old('role') == 'Staf' ? 'selected' : '' }}>Staf</option>
            <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_bagian_id" class="form-label">Bagian</label>
        <select name="bagian_id" class="form-select" id="add_bagian_id">
            <option value="">Pilih Bagian</option>
            @foreach($bagian ?? [] as $bagianItem)
                <option value="{{ $bagianItem->id }}" {{ old('bagian_id') == $bagianItem->id ? 'selected' : '' }}>
                    {{ $bagianItem->nama_bagian }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <div class="form-check">
            <input type="checkbox" name="is_kepala_bagian" class="form-check-input" id="add_is_kepala_bagian" value="1" {{ old('is_kepala_bagian') ? 'checked' : '' }}>
            <label for="add_is_kepala_bagian" class="form-check-label">
                Kepala Bagian
            </label>
        </div>
        <div class="form-text">Centang jika user ini adalah kepala bagian</div>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" aria-label="close" data-bs-dismiss="modal" id="addUserCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="addUserSubmitBtn">
            Simpan
        </button>
    </div>
</form>
