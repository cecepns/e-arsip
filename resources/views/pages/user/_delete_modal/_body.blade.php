<p>Apakah Anda yakin ingin menghapus user <strong id="deleteUserName"></strong>?</p>
<p class="text-muted">Data akan dihapus secara soft delete untuk menjaga integritas data relasi.</p>

<div class="d-flex justify-content-end mt-3">
    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="deleteUserCancelBtn">Batal</button>
    <form id="deleteUserForm" method="POST" style="display: inline;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-danger" id="deleteUserSubmitBtn">Ya, Hapus</button>
    </form>
</div>

@push('scripts')
<script>
    /**
     * ANCHOR: Show Delete User Modal
     * Show the delete user modal and populate with data
     * @param {number} userId - The id of the user to delete
     */
    const showDeleteUserModal = (userId) => {
        const deleteUserName = document.getElementById('deleteUserName');
        const deleteUserForm = document.getElementById('deleteUserForm');

        const user = usersDataCurrentPage.find(user => user.id === userId);
        const { id, username } = user;

        deleteUserName.textContent = username;
        deleteUserForm.action = `/user/${id}`;
    }

    /**
     * ANCHOR: Delete User Handlers
     * Handle the delete user form submission
     */
    const deleteUserHandlers = () => {
        const deleteUserForm = document.getElementById('deleteUserForm');
        const deleteUserSubmitBtn = document.getElementById('deleteUserSubmitBtn');
        const deleteUserCancelBtn = document.getElementById('deleteUserCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        deleteUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(deleteUserForm);
            setLoadingState(true, deleteUserSubmitBtn);

            try {
                const formData = new FormData(deleteUserForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(deleteUserForm.action, {
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
                    deleteUserForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalDeleteUser')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, deleteUserForm);
                }
            } catch (error) {
                handleErrorResponse(error, deleteUserForm);
            } finally {
                setLoadingState(false, deleteUserSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetDeleteFormOnModalClose = () => {
        const modalDeleteUser = document.getElementById('modalDeleteUser');
        const deleteUserForm = document.getElementById('deleteUserForm');
        
        modalDeleteUser.addEventListener('hidden.bs.modal', function() {
            // Reset form
            deleteUserForm.reset();
            
            // Clear validation errors
            clearErrors(deleteUserForm);
            
            // Reset loading state if any
            const deleteUserSubmitBtn = document.getElementById('deleteUserSubmitBtn');
            setLoadingState(false, deleteUserSubmitBtn);
        });
    }

    // ANCHOR: Initialize delete user handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        deleteUserHandlers();
        resetDeleteFormOnModalClose();
    });
</script>
@endpush
