<p>Apakah Anda yakin ingin menghapus surat keluar <strong id="deleteSuratKeluarName"></strong>?</p>
<p class="text-muted">Data akan dihapus secara soft delete untuk menjaga integritas data relasi.</p>

<div class="d-flex justify-content-end mt-3">
    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="deleteSuratKeluarCancelBtn">Batal</button>
    <form id="deleteSuratKeluarForm" method="POST" style="display: inline;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-danger" id="deleteSuratKeluarSubmitBtn">Ya, Hapus</button>
    </form>
</div>

@push('scripts')
<script>
    /**
     * ANCHOR: Show Delete Surat Keluar Modal
     * Show the delete surat keluar modal and populate with data
     * @param {number} suratKeluarId - The id of the surat keluar to delete
     */
    const showDeleteSuratKeluarModal = (suratKeluarId) => {
        const deleteSuratKeluarName = document.getElementById('deleteSuratKeluarName');
        const deleteSuratKeluarForm = document.getElementById('deleteSuratKeluarForm');

        const suratKeluar = suratKeluarDataCurrentPage.find(surat => surat.id === suratKeluarId);
        const { id, nomor_surat } = suratKeluar;

        deleteSuratKeluarName.textContent = nomor_surat;
        deleteSuratKeluarForm.action = `/surat-keluar/${id}`;
    }

    /**
     * ANCHOR: Delete Surat Keluar Handlers
     * Handle the delete surat keluar form submission
     */
    const deleteSuratKeluarHandlers = () => {
        const deleteSuratKeluarForm = document.getElementById('deleteSuratKeluarForm');
        const deleteSuratKeluarSubmitBtn = document.getElementById('deleteSuratKeluarSubmitBtn');
        const deleteSuratKeluarCancelBtn = document.getElementById('deleteSuratKeluarCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        deleteSuratKeluarForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(deleteSuratKeluarForm);
            setLoadingState(true, deleteSuratKeluarSubmitBtn);

            try {
                const formData = new FormData(deleteSuratKeluarForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(deleteSuratKeluarForm.action, {
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
                    deleteSuratKeluarForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalDeleteSuratKeluar')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, deleteSuratKeluarForm);
                }
            } catch (error) {
                handleErrorResponse(error, deleteSuratKeluarForm);
            } finally {
                setLoadingState(false, deleteSuratKeluarSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetDeleteSuratKeluarFormOnModalClose = () => {
        const modalDeleteSuratKeluar = document.getElementById('modalDeleteSuratKeluar');
        const deleteSuratKeluarForm = document.getElementById('deleteSuratKeluarForm');
        
        modalDeleteSuratKeluar.addEventListener('hidden.bs.modal', function() {
            // Reset form
            deleteSuratKeluarForm.reset();
            
            // Clear validation errors
            clearErrors(deleteSuratKeluarForm);
            
            // Reset loading state if any
            const deleteSuratKeluarSubmitBtn = document.getElementById('deleteSuratKeluarSubmitBtn');
            setLoadingState(false, deleteSuratKeluarSubmitBtn);
        });
    }

    // ANCHOR: Initialize delete surat keluar handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        deleteSuratKeluarHandlers();
        resetDeleteSuratKeluarFormOnModalClose();
    });
</script>
@endpush
