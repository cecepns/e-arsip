<form id="addUserForm" action="{{ route('user.store') }}" method="POST">
    @csrf
    
    
    <div class="mb-3">
        <label for="add_username" class="form-label">Username <span class="text-danger">*</span></label>
        <input type="text" name="username" class="form-control" id="add_username" placeholder="Username" value="{{ old('username') }}">
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control" id="add_nama" placeholder="Nama Lengkap" value="{{ old('nama') }}">
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" id="add_email" placeholder="email@example.com" value="{{ old('email') }}">
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_phone" class="form-label">Nomor Telepon</label>
        <input type="text" name="phone" class="form-control" id="add_phone" placeholder="081234567890" value="{{ old('phone') }}">
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_password" class="form-label">Password <span class="text-danger">*</span></label>
        <input type="text" name="password" class="form-control" id="add_password" placeholder="Password" value="{{ old('password') }}">
        <div class="invalid-feedback"></div>
        <div class="form-text">Password minimal 8 karakter, harus mengandung huruf besar, huruf kecil, angka, dan simbol (@$!%*?&.)</div>
    </div>
    
    <div class="mb-3">
        <label for="add_role" class="form-label">Role <span class="text-danger">*</span></label>
        <select name="role" class="form-select" id="add_role">
            <option value="">Pilih Role</option>
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
        <div class="form-text">Centang jika user ini akan menjadi kepala bagian. Pastikan bagian sudah dipilih. <strong>Admin tidak bisa menjadi kepala bagian.</strong></div>
        <div class="invalid-feedback"></div>
    </div>
    
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="addUserCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="addUserSubmitBtn">Simpan</button>
    </div>
</form>

@push('scripts')
<script>
    /**
     * ANCHOR: Add User Handlers
     * Handle the add user form submission
     */
    const addUserHandlers = () => {
        const addUserForm = document.getElementById('addUserForm');
        const addUserSubmitBtn = document.getElementById('addUserSubmitBtn');
        const addUserCancelBtn = document.getElementById('addUserCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        if (addUserForm) {
            addUserForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors(addUserForm);
                setLoadingState(true, addUserSubmitBtn);

                try {
                    const formData = new FormData(addUserForm);
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000);
                    const response = await fetchWithRetry(addUserForm.action, {
                        method: 'POST',
                        body: formData,
                        signal: controller.signal,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    clearTimeout(timeoutId);
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showToast(data.message, 'success', 5000);
                        addUserForm.reset();
                        bootstrap.Modal.getInstance(document.getElementById('modalAddUser')).hide();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        handleErrorResponse(data, addUserForm);
                    }
                } catch (error) {
                    handleErrorResponse(error, addUserForm);
                } finally {
                    setLoadingState(false, addUserSubmitBtn);
                }
            });
        }
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetFormOnModalClose = () => {
        const modalAddUser = document.getElementById('modalAddUser');
        const addUserForm = document.getElementById('addUserForm');
        
        modalAddUser.addEventListener('hidden.bs.modal', function() {
            // Reset form
            addUserForm.reset();
            
            // Clear validation errors
            clearErrors(addUserForm);
            
            // Reset loading state if any
            const addUserSubmitBtn = document.getElementById('addUserSubmitBtn');
            setLoadingState(false, addUserSubmitBtn);
        });
    }

    /**
     * ANCHOR: Handle checkbox kepala bagian
     * Enable/disable checkbox based on bagian selection
     */
    const handleKepalaBagianCheckbox = () => {
        const addBagianSelect = document.getElementById('add_bagian_id');
        const addKepalaBagianCheckbox = document.getElementById('add_is_kepala_bagian');
        const addRoleSelect = document.getElementById('add_role');

        // Handle add form
        if (addBagianSelect && addKepalaBagianCheckbox && addRoleSelect) {
            // Function to update bagian and kepala bagian based on role
            const updateBagianAndKepalaBagian = () => {
                if (addRoleSelect.value === 'Admin') {
                    // Admin role: disable bagian and kepala bagian
                    addBagianSelect.disabled = true;
                    addBagianSelect.value = '';
                    addKepalaBagianCheckbox.disabled = true;
                    addKepalaBagianCheckbox.checked = false;
                } else {
                    // Non-admin role: enable bagian selection
                    addBagianSelect.disabled = false;
                    addKepalaBagianCheckbox.disabled = addBagianSelect.value === '';
                }
            };

            // Listen for role changes
            addRoleSelect.addEventListener('change', updateBagianAndKepalaBagian);

            // Listen for bagian changes (only for non-admin roles)
            addBagianSelect.addEventListener('change', function() {
                if (addRoleSelect.value !== 'Admin') {
                    if (this.value) {
                        addKepalaBagianCheckbox.disabled = false;
                    } else {
                        addKepalaBagianCheckbox.disabled = true;
                        addKepalaBagianCheckbox.checked = false;
                    }
                }
            });

            // Initialize state
            updateBagianAndKepalaBagian();
        }
    }

    // ANCHOR: Initialize add user handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        addUserHandlers();
        resetFormOnModalClose();
        handleKepalaBagianCheckbox();
    });
</script>
@endpush
