<form id="addUserForm" action="{{ route('user.store') }}" method="POST">
    @csrf
    
    <div class="mb-3">
        <label for="add_username" class="form-label">Username</label>
        <input type="text" name="username" class="form-control" id="add_username" placeholder="Username" value="{{ old('username') }}" required>
    </div>
    
    <div class="mb-3">
        <label for="add_email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" id="add_email" placeholder="email@example.com" value="{{ old('email') }}" required>
    </div>
    
    <div class="mb-3">
        <label for="add_password" class="form-label">Password</label>
        <input type="text" name="password" class="form-control" id="add_password" placeholder="Password" value="{{ old('password') }}" required>
        <div class="form-text">Password akan disimpan dalam bentuk plain text</div>
    </div>
    
    <div class="mb-3">
        <label for="add_role" class="form-label">Role</label>
        <select name="role" class="form-select" id="add_role" required>
            <option value="Staf" {{ old('role') == 'Staf' ? 'selected' : '' }}>Staf</option>
            <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
        </select>
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
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" aria-label="close" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
