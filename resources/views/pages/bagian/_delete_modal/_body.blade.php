<p>Apakah Anda yakin ingin menghapus bagian <strong id="deleteBagianName"></strong>?</p>
<p class="text-muted">Data akan dihapus secara soft delete untuk menjaga integritas data relasi.</p>

<div class="d-flex justify-content-end mt-3">
    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="deleteBagianCancelBtn">Batal</button>
    <form id="deleteBagianForm" method="POST" style="display: inline;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-danger" id="deleteBagianSubmitBtn">Ya, Hapus</button>
    </form>
</div>

@push('scripts')
<script>
    /**
     * ANCHOR: Show Delete Bagian Modal
     * Show the delete bagian modal and populate with data
     * @param {number} bagianId - The id of the bagian to delete
     */
    const showDeleteBagianModal = (bagianId) => {
        const deleteBagianName = document.getElementById('deleteBagianName');
        const deleteBagianForm = document.getElementById('deleteBagianForm');

        const bagian = window.bagianDataCurrentPage.find(bagian => bagian.id === bagianId);
        const { id, nama_bagian } = bagian;

        deleteBagianName.textContent = nama_bagian;
        deleteBagianForm.action = `/bagian/${id}`;
    }

    /**
     * ANCHOR: Delete Bagian Handlers
     * Handle the delete bagian form submission
     */
    const deleteBagianHandlers = () => {
        const deleteBagianForm = document.getElementById('deleteBagianForm');
        const deleteBagianSubmitBtn = document.getElementById('deleteBagianSubmitBtn');
        const deleteBagianCancelBtn = document.getElementById('deleteBagianCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        deleteBagianForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(deleteBagianForm);
            setLoadingState(true, deleteBagianSubmitBtn);

            try {
                const formData = new FormData(deleteBagianForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(deleteBagianForm.action, {
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
                    deleteBagianForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalDeleteBagian')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, deleteBagianForm);
                }
            } catch (error) {
                handleErrorResponse(error, deleteBagianForm);
            } finally {
                setLoadingState(false, deleteBagianSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetDeleteFormOnModalClose = () => {
        const modalDeleteBagian = document.getElementById('modalDeleteBagian');
        const deleteBagianForm = document.getElementById('deleteBagianForm');
        
        modalDeleteBagian.addEventListener('hidden.bs.modal', function() {
            // Reset form
            deleteBagianForm.reset();
            
            // Clear validation errors
            clearErrors(deleteBagianForm);
            
            // Reset loading state if any
            const deleteBagianSubmitBtn = document.getElementById('deleteBagianSubmitBtn');
            setLoadingState(false, deleteBagianSubmitBtn);
        });
    }

    // ANCHOR: Initialize delete bagian handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        deleteBagianHandlers();
        resetDeleteFormOnModalClose();
    });
</script>
@endpush