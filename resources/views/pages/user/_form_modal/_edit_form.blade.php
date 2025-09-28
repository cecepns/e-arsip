<form id="editUserForm" action="" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="user_id" id="edit_user_id" value="">
    
    <div class="mb-3">
        <label for="edit_username" class="form-label">Username <span class="text-danger">*</span></label>
        <input type="text" name="username" class="form-control" id="edit_username" placeholder="Username" required>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control" id="edit_nama" placeholder="Nama Lengkap" required>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" id="edit_email" placeholder="email@example.com" required>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_phone" class="form-label">Nomor Telepon</label>
        <input type="text" name="phone" class="form-control" id="edit_phone" placeholder="081234567890">
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_password" class="form-label">Password</label>
        <div class="input-group">
            <input type="password" name="password" class="form-control" id="edit_password" placeholder="Kosongkan jika tidak ingin mengubah password">
            <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('edit_password')">
                <i class="fas fa-eye" id="edit_password_icon"></i>
            </button>
        </div>
        <div class="form-text">Kosongkan jika tidak ingin mengubah password. Jika diisi, minimal 8 karakter dengan huruf besar, huruf kecil, angka, dan simbol</div>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
        <select name="role" class="form-select" id="edit_role" required>
            <option value="">Pilih Role</option>
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
        <div class="form-text">Centang jika user ini akan menjadi kepala bagian. Pastikan bagian sudah dipilih. <strong>Admin tidak bisa menjadi kepala bagian.</strong></div>
        <div class="invalid-feedback"></div>
    </div>
    
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="editUserCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="editUserSubmitBtn">Update</button>
    </div>
</form>

@push('scripts')
<script>
    /**
     * ANCHOR: Show Edit User Modal
     * Show the edit user modal and populate with data
     * @param {number} userId - The id of the user to edit
     */
    const showEditUserModal = (userId) => {
        const editUserForm = document.getElementById('editUserForm');
        const idInput = document.getElementById('edit_user_id');
        const usernameInput = document.getElementById('edit_username');
        const namaInput = document.getElementById('edit_nama');
        const emailInput = document.getElementById('edit_email');
        const phoneInput = document.getElementById('edit_phone');
        const passwordInput = document.getElementById('edit_password');
        const roleInput = document.getElementById('edit_role');
        const bagianInput = document.getElementById('edit_bagian_id');
        const isKepalaBagianInput = document.getElementById('edit_is_kepala_bagian');
        const user = usersDataCurrentPage.find(user => user.id === userId);
        const { id, username, nama, email, phone, password, role, bagian_id } = user;

        idInput.value = id;
        usernameInput.value = username || '';
        namaInput.value = nama || '';
        emailInput.value = email || '';
        phoneInput.value = phone || '';
        passwordInput.value = '';
        roleInput.value = role || '';
        bagianInput.value = bagian_id || '';
        
        // Check if user is kepala bagian
        isKepalaBagianInput.checked = user.bagian && user.bagian.kepala_bagian_user_id == id;

        editUserForm.action = `/user/${id}`;
        
        // Initialize Admin role restrictions
        updateEditBagianAndKepalaBagian();
    }

    /**
     * ANCHOR: Edit User Handlers
     * Handle the edit user form submission
     */
    const editUserHandlers = () => {
        const editUserForm = document.getElementById('editUserForm');
        const editUserSubmitBtn = document.getElementById('editUserSubmitBtn');
        const editUserCancelBtn = document.getElementById('editUserCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        editUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(editUserForm);
            setLoadingState(true, editUserSubmitBtn);

            try {
                const formData = new FormData(editUserForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(editUserForm.action, {
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
                    editUserForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalEditUser')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, editUserForm);
                }
            } catch (error) {
                handleErrorResponse(error, editUserForm);
            } finally {
                setLoadingState(false, editUserSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Handle checkbox kepala bagian for edit form
     * Enable/disable checkbox based on bagian selection and role
     */
    const handleEditKepalaBagianCheckbox = () => {
        const editBagianSelect = document.getElementById('edit_bagian_id');
        const editKepalaBagianCheckbox = document.getElementById('edit_is_kepala_bagian');
        const editRoleSelect = document.getElementById('edit_role');

        // Handle edit form
        if (editBagianSelect && editKepalaBagianCheckbox && editRoleSelect) {
            // Function to update bagian and kepala bagian based on role
            const updateEditBagianAndKepalaBagian = () => {
                if (editRoleSelect.value === 'Admin') {
                    // Admin role: disable bagian and kepala bagian
                    editBagianSelect.disabled = true;
                    editBagianSelect.value = '';
                    editKepalaBagianCheckbox.disabled = true;
                    editKepalaBagianCheckbox.checked = false;
                } else {
                    // Non-admin role: enable bagian selection
                    editBagianSelect.disabled = false;
                    editKepalaBagianCheckbox.disabled = editBagianSelect.value === '';
                }
            };

            // Listen for role changes
            editRoleSelect.addEventListener('change', updateEditBagianAndKepalaBagian);

            // Listen for bagian changes (only for non-admin roles)
            editBagianSelect.addEventListener('change', function() {
                if (editRoleSelect.value !== 'Admin') {
                    if (this.value) {
                        editKepalaBagianCheckbox.disabled = false;
                    } else {
                        editKepalaBagianCheckbox.disabled = true;
                        editKepalaBagianCheckbox.checked = false;
                    }
                }
            });

            // Make function globally accessible for showEditUserModal
            window.updateEditBagianAndKepalaBagian = updateEditBagianAndKepalaBagian;
        }
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetEditFormOnModalClose = () => {
        const modalEditUser = document.getElementById('modalEditUser');
        const editUserForm = document.getElementById('editUserForm');
        
        modalEditUser.addEventListener('hidden.bs.modal', function() {
            // Reset form
            editUserForm.reset();
            
            // Clear validation errors
            clearErrors(editUserForm);
            
            // Reset loading state if any
            const editUserSubmitBtn = document.getElementById('editUserSubmitBtn');
            setLoadingState(false, editUserSubmitBtn);
        });
    }

    // ANCHOR: Initialize edit user handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        editUserHandlers();
        handleEditKepalaBagianCheckbox();
        resetEditFormOnModalClose();
    });
</script>
@endpush
