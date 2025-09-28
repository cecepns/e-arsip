<p>Apakah Anda yakin ingin menghapus surat masuk <strong id="deleteSuratMasukName"></strong>?</p>
<p class="text-muted">Data akan dihapus secara soft delete untuk menjaga integritas data relasi.</p>

<div class="d-flex justify-content-end mt-3">
    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="deleteSuratMasukCancelBtn">Batal</button>
    <form id="deleteSuratMasukForm" method="POST" style="display: inline;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-danger" id="deleteSuratMasukSubmitBtn">Ya, Hapus</button>
    </form>
</div>

@push('scripts')
<script>
    /**
     * ANCHOR: Show Delete Surat Masuk Modal
     * Show the delete surat masuk modal and populate with data
     * @param {number} suratMasukId - The id of the surat masuk to delete
     */
    const showDeleteSuratMasukModal = (suratMasukId) => {
        const deleteSuratMasukName = document.getElementById('deleteSuratMasukName');
        const deleteSuratMasukForm = document.getElementById('deleteSuratMasukForm');

        const suratMasuk = suratMasukDataCurrentPage.data.find(surat => surat.id === suratMasukId);
        const { id, nomor_surat } = suratMasuk;

        deleteSuratMasukName.textContent = nomor_surat;
        deleteSuratMasukForm.action = `/surat-masuk/${id}`;
    }

    /**
     * ANCHOR: Delete Surat Masuk Handlers
     * Handle the delete surat masuk form submission
     */
    const deleteSuratMasukHandlers = () => {
        const deleteSuratMasukForm = document.getElementById('deleteSuratMasukForm');
        const deleteSuratMasukSubmitBtn = document.getElementById('deleteSuratMasukSubmitBtn');
        const deleteSuratMasukCancelBtn = document.getElementById('deleteSuratMasukCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        deleteSuratMasukForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(deleteSuratMasukForm);
            setLoadingState(true, deleteSuratMasukSubmitBtn);

            try {
                const formData = new FormData(deleteSuratMasukForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(deleteSuratMasukForm.action, {
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
                    deleteSuratMasukForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalDeleteSuratMasuk')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, deleteSuratMasukForm);
                }
            } catch (error) {
                handleErrorResponse(error, deleteSuratMasukForm);
            } finally {
                setLoadingState(false, deleteSuratMasukSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetDeleteSuratMasukFormOnModalClose = () => {
        const modalDeleteSuratMasuk = document.getElementById('modalDeleteSuratMasuk');
        const deleteSuratMasukForm = document.getElementById('deleteSuratMasukForm');
        
        modalDeleteSuratMasuk.addEventListener('hidden.bs.modal', function() {
            // Reset form
            deleteSuratMasukForm.reset();
            
            // Clear validation errors
            clearErrors(deleteSuratMasukForm);
            
            // Reset loading state if any
            const deleteSuratMasukSubmitBtn = document.getElementById('deleteSuratMasukSubmitBtn');
            setLoadingState(false, deleteSuratMasukSubmitBtn);
        });
    }

    // ANCHOR: Initialize delete surat masuk handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        deleteSuratMasukHandlers();
        resetDeleteSuratMasukFormOnModalClose();
    });
</script>
@endpush
