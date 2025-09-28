<form id="editSuratMasukForm" action="" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" id="edit_surat_masuk_id">
    
    {{-- Section 1: Data Surat Masuk --}}
    <div class="mb-4">
        <h5 class="mb-4">Data Surat Masuk</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="edit_nomor_surat" class="form-label">Nomor Surat</label>
                    <input type="text" name="nomor_surat" class="form-control" id="edit_nomor_surat" placeholder="Nomor Surat" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_tanggal_surat" class="form-label">Tanggal Surat</label>
                    <input type="date" name="tanggal_surat" class="form-control" id="edit_tanggal_surat" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_tanggal_terima" class="form-label">Tanggal Terima</label>
                    <input type="date" name="tanggal_terima" class="form-control" id="edit_tanggal_terima" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_perihal" class="form-label">Perihal</label>
                    <input type="text" name="perihal" class="form-control" id="edit_perihal" placeholder="Perihal Surat" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_pengirim" class="form-label">Pengirim</label>
                    <input type="text" name="pengirim" class="form-control" id="edit_pengirim" placeholder="Pengirim Surat" required>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_sifat_surat" class="form-label">Sifat Surat</label>
                    <select name="sifat_surat" class="form-select" id="edit_sifat_surat" required>
                        <option value="">Pilih Sifat Surat</option>
                        <option value="Biasa">Biasa</option>
                        <option value="Segera">Segera</option>
                        <option value="Penting">Penting</option>
                        <option value="Rahasia">Rahasia</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_tujuan_bagian_id" class="form-label">Bagian Tujuan</label>
                    <select name="tujuan_bagian_id" class="form-select" id="edit_tujuan_bagian_id" required>
                        <option value="">Pilih Bagian</option>
                        @foreach($bagian ?? [] as $bagianItem)
                            <option value="{{ $bagianItem->id }}">{{ $bagianItem->nama_bagian }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="edit_lampiran_pdf" class="form-label">Lampiran Surat Utama (PDF)</label>
                    <input type="file" name="lampiran_pdf" class="form-control" id="edit_lampiran_pdf" accept="application/pdf">
                    <div class="form-text">File PDF maksimal 20MB. Kosongkan jika tidak ingin mengubah</div>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_lampiran_pendukung" class="form-label">Dokumen Pendukung</label>
                    <input type="file" name="lampiran_pendukung[]" class="form-control" id="edit_lampiran_pendukung" multiple accept=".zip,.rar,.docx,.xlsx">
                    <div class="form-text">File ZIP, RAR, DOCX, XLSX maksimal 20MB</div>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_ringkasan_isi" class="form-label">Ringkasan Isi</label>
                    <textarea name="ringkasan_isi" class="form-control" id="edit_ringkasan_isi" rows="5" placeholder="Ringkasan isi surat"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" id="edit_keterangan" rows="3" placeholder="Keterangan tambahan"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2: Disposisi --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Disposisi</h5>
            <button type="button" class="btn btn-primary" id="edit_add_disposisi_btn">
                Tambah Disposisi
            </button>
        </div>
        <div class="mt-3">
            <div class="row" id="edit_disposisi_container">
                <!-- Disposisi cards will be added here dynamically -->
            </div>
            <div id="edit_disposisi_empty_state" class="text-center text-muted py-4">
                <i class="fas fa-share-alt fa-3x mb-3"></i>
                <h6>Belum ada disposisi</h6>
                <p class="mb-0">Surat ini belum memiliki disposisi. Klik "Tambah Disposisi" untuk menambahkan disposisi baru.</p>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" aria-label="close" data-bs-dismiss="modal" id="editSuratMasukCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="editSuratMasukSubmitBtn">
            Update
        </button>
    </div>
</form>

@push('scripts')
<script>
    /**
     * ANCHOR: Edit Surat Masuk Handlers
     * Handle the edit surat masuk form submission
     */
    const editSuratMasukHandlers = () => {
        const editSuratMasukForm = document.getElementById('editSuratMasukForm');
        const editSuratMasukSubmitBtn = document.getElementById('editSuratMasukSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        if (editSuratMasukForm) {
            editSuratMasukForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors(editSuratMasukForm);
                setLoadingState(true, editSuratMasukSubmitBtn);

                try {
                    const formData = new FormData(editSuratMasukForm);
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000);
                    const response = await fetchWithRetry(editSuratMasukForm.action, {
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
                        editSuratMasukForm.reset();
                        bootstrap.Modal.getInstance(document.getElementById('modalEditSuratMasuk')).hide();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        handleErrorResponse(data, editSuratMasukForm);
                    }
                } catch (error) {
                    handleErrorResponse(error, editSuratMasukForm);
                } finally {
                    setLoadingState(false, editSuratMasukSubmitBtn);
                }
            });
        }
    }

    /**
     * ANCHOR: Edit from Detail Modal
     * Open edit modal from detail modal
     */
    const editFromDetail = () => {
        if (window.currentDetailSuratMasukId) {
            // Close detail modal
            bootstrap.Modal.getInstance(document.getElementById('modalDetailSuratMasuk')).hide();
            
            // Open edit modal after a short delay
            setTimeout(() => {
                showEditSuratMasukModal(window.currentDetailSuratMasukId);
                bootstrap.Modal.getInstance(document.getElementById('modalEditSuratMasuk')).show();
            }, 300);
        }
    };

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetEditSuratMasukFormOnModalClose = () => {
        const modalEditSuratMasuk = document.getElementById('modalEditSuratMasuk');
        const editSuratMasukForm = document.getElementById('editSuratMasukForm');
        
        if (modalEditSuratMasuk && editSuratMasukForm) {
            modalEditSuratMasuk.addEventListener('hidden.bs.modal', function() {
                // Re-enable form
                editSuratMasukForm.style.pointerEvents = 'auto';
                
                // Clear validation errors
                clearErrors(editSuratMasukForm);
                
                // Reset loading state if any
                const editSuratMasukSubmitBtn = document.getElementById('editSuratMasukSubmitBtn');
                setLoadingState(false, editSuratMasukSubmitBtn);
                editSuratMasukSubmitBtn.disabled = false;
                
                // Clear all disposisi fields
                if (window.editDisposisiManager) {
                    window.editDisposisiManager.clearAllDisposisiFields();
                }
            });
        }
    }

    // ANCHOR: Initialize edit surat masuk handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        editSuratMasukHandlers();
        resetEditSuratMasukFormOnModalClose();
        
        // Initialize disposisi manager for edit form
        window.editDisposisiManager = new DisposisiManager('edit_');
        window.editDisposisiManager.initialize();
    });
</script>
@endpush
