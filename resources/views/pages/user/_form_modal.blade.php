<form id="userForm" action="{{ route('user.store') }}" method="POST">
    @csrf
    <input type="hidden" name="_method" id="formMethod" value="POST">
    <input type="hidden" name="user_id" id="user_id" value="">
    
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" class="form-control" id="username" placeholder="Username" value="{{ old('username') }}" required>
    </div>
    
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" id="email" placeholder="email@example.com" value="{{ old('email') }}" required>
    </div>
    
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="text" name="password" class="form-control" id="password" placeholder="Password" value="{{ old('password') }}" required>
        <div class="form-text" id="passwordHelp">Password akan disimpan dalam bentuk plain text</div>
    </div>
    
    <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select name="role" class="form-select" id="role" required>
            <option value="Staf" {{ old('role') == 'Staf' ? 'selected' : '' }}>Staf</option>
            <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="bagian_id" class="form-label">Bagian</label>
        <select name="bagian_id" class="form-select" id="bagian_id">
            <option value="">Pilih Bagian</option>
            @foreach($bagian ?? [] as $bagianItem)
                <option value="{{ $bagianItem->id }}" {{ old('bagian_id') == $bagianItem->id ? 'selected' : '' }}>
                    {{ $bagianItem->nama_bagian }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
    </div>
</form>
