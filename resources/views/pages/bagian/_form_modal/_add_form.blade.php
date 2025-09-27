<form id="addBagianForm" action="{{ route('bagian.store') }}" method="POST">
    @csrf
    <input type="hidden" name="_method" value="POST">
    
    <div class="mb-3">
        <label for="add_nama_bagian" class="form-label">Nama Bagian <span class="text-danger">*</span></label>
        <input type="text" name="nama_bagian" class="form-control" id="add_nama_bagian" placeholder="Nama Bagian" value="{{ old('nama_bagian') }}" required>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_kepala_bagian_user_id" class="form-label">Kepala Bagian</label>
        <select name="kepala_bagian_user_id" class="form-select" id="add_kepala_bagian_user_id">
            <option value="">Pilih Kepala Bagian (Opsional)</option>
            @foreach($usersNotMarkedAsKepalaBagian as $user)
                <option value="{{ $user->id }}" {{ old('kepala_bagian_user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->nama ?? $user->username }} ({{ $user->bagian->nama_bagian ?? 'Tanpa Bagian' }})
                </option>
            @endforeach
        </select>
        <div class="form-text">Pilih user yang akan menjadi kepala bagian. Kosongkan jika belum ada user.</div>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" id="add_status" required>
            <option value="">Pilih Status</option>
            <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="add_keterangan" class="form-label">Deskripsi</label>
        <textarea name="keterangan" class="form-control" id="add_keterangan" rows="3" placeholder="Deskripsi bagian">{{ old('keterangan') }}</textarea>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="addBagianCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="addBagianSubmitBtn">Simpan</button>
    </div>
</form>

@push('scripts')
<script>
    /**
     * ANCHOR: Add Bagian Handlers
     * Handle the add bagian form submission
     */
    const addBagianHandlers = () => {
        const addBagianForm = document.getElementById('addBagianForm');
        const addBagianSubmitBtn = document.getElementById('addBagianSubmitBtn');
        const addBagianCancelBtn = document.getElementById('addBagianCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        addBagianForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(addBagianForm);
            setLoadingState(true, addBagianSubmitBtn);

            try {
                const formData = new FormData(addBagianForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(addBagianForm.action, {
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
                    addBagianForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalAddBagian')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, addBagianForm);
                }
            } catch (error) {
                handleErrorResponse(error, addBagianForm);
            } finally {
                setLoadingState(false, addBagianSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetFormOnModalClose = () => {
        const modalAddBagian = document.getElementById('modalAddBagian');
        const addBagianForm = document.getElementById('addBagianForm');
        
        modalAddBagian.addEventListener('hidden.bs.modal', function() {
            // Reset form
            addBagianForm.reset();
            
            // Clear validation errors
            clearErrors(addBagianForm);
            
            // Reset loading state if any
            const addBagianSubmitBtn = document.getElementById('addBagianSubmitBtn');
            setLoadingState(false, addBagianSubmitBtn);
        });
    }

    // ANCHOR: Initialize add bagian handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        addBagianHandlers();
        resetFormOnModalClose();
    });
</script>
@endpush
