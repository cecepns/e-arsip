<form id="editBagianForm" action="" method="POST">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="bagian_id" id="edit_bagian_id" value="">
    
    <div class="mb-3">
        <label for="edit_nama_bagian" class="form-label">Nama Bagian <span class="text-danger">*</span></label>
        <input type="text" name="nama_bagian" class="form-control" id="edit_nama_bagian" placeholder="Nama Bagian" required>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_kepala_bagian_user_id" class="form-label">Kepala Bagian</label>
        <select name="kepala_bagian_user_id" class="form-select" id="edit_kepala_bagian_user_id">
            <option value="">Pilih Kepala Bagian (Opsional)</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">
                    {{ $user->nama ?? $user->username }} ({{ $user->bagian->nama_bagian ?? 'Tanpa Bagian' }})
                </option>
            @endforeach
        </select>
        <div class="form-text">Pilih user yang akan menjadi kepala bagian. Kosongkan jika belum ada user.</div>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" id="edit_status" required>
            <option value="">Pilih Status</option>
            <option value="Aktif">Aktif</option>
            <option value="Nonaktif">Tidak Aktif</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="edit_keterangan" class="form-label">Deskripsi</label>
        <textarea name="keterangan" class="form-control" id="edit_keterangan" rows="3" placeholder="Deskripsi bagian"></textarea>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="editBagianCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="editBagianSubmitBtn">Simpan</button>
    </div>
</form>

@push('scripts')
<script>
    /**
     * ANCHOR: Show Edit Bagian Modal
     * Show the edit bagian modal and populate form with data
     * @param {number} bagianId - The id of the bagian to edit
     */
    const showEditBagianModal = (bagianId) => {
        const editBagianForm = document.getElementById('editBagianForm');
        const idInput = document.getElementById('edit_bagian_id');
        const namaInput = document.getElementById('edit_nama_bagian');
        const kepalaInput = document.getElementById('edit_kepala_bagian_user_id');
        const statusInput = document.getElementById('edit_status');
        const keteranganInput = document.getElementById('edit_keterangan');

        // Find bagian data from global variable
        const bagian = window.bagianDataCurrentPage.find(bagian => bagian.id === bagianId);
        const { id, nama_bagian, kepala_bagian_user_id, status, keterangan } = bagian;

        // Populate form fields
        idInput.value = id;
        namaInput.value = nama_bagian || '';
        kepalaInput.value = kepala_bagian_user_id || '';
        statusInput.value = status || '';
        keteranganInput.value = keterangan || '';

        // Set form action
        editBagianForm.action = `/bagian/${id}`;
    }

    /**
     * ANCHOR: Edit Bagian Handlers
     * Handle the edit bagian form submission
     */
    const editBagianHandlers = () => {
        const editBagianForm = document.getElementById('editBagianForm');
        const editBagianSubmitBtn = document.getElementById('editBagianSubmitBtn');
        const editBagianCancelBtn = document.getElementById('editBagianCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        editBagianForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(editBagianForm);
            setLoadingState(true, editBagianSubmitBtn);

            try {
                const formData = new FormData(editBagianForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(editBagianForm.action, {
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
                    editBagianForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalEditBagian')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, editBagianForm);
                }
            } catch (error) {
                handleErrorResponse(error, editBagianForm);
            } finally {
                setLoadingState(false, editBagianSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetEditFormOnModalClose = () => {
        const modalEditBagian = document.getElementById('modalEditBagian');
        const editBagianForm = document.getElementById('editBagianForm');
        
        modalEditBagian.addEventListener('hidden.bs.modal', function() {
            // Reset form
            editBagianForm.reset();
            
            // Clear validation errors
            clearErrors(editBagianForm);
            
            // Reset loading state if any
            const editBagianSubmitBtn = document.getElementById('editBagianSubmitBtn');
            setLoadingState(false, editBagianSubmitBtn);
        });
    }

    // ANCHOR: Initialize edit bagian handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        editBagianHandlers();
        resetEditFormOnModalClose();
    });

    // ANCHOR: Make showEditBagianModal globally accessible
    window.showEditBagianModal = showEditBagianModal;
</script>
@endpush
