<form id="addSuratMasukForm" action="{{ route('surat_masuk.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="add_nomor_surat" class="form-label">Nomor Surat</label>
                <input type="text" name="nomor_surat" class="form-control" id="add_nomor_surat" placeholder="Nomor Surat" value="{{ old('nomor_surat') }}" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_tanggal_surat" class="form-label">Tanggal Surat</label>
                <input type="date" name="tanggal_surat" class="form-control" id="add_tanggal_surat" value="{{ old('tanggal_surat') }}" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_tanggal_terima" class="form-label">Tanggal Terima</label>
                <input type="date" name="tanggal_terima" class="form-control" id="add_tanggal_terima" value="{{ old('tanggal_terima') }}" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_perihal" class="form-label">Perihal</label>
                <input type="text" name="perihal" class="form-control" id="add_perihal" placeholder="Perihal Surat" value="{{ old('perihal') }}" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_pengirim" class="form-label">Pengirim</label>
                <input type="text" name="pengirim" class="form-control" id="add_pengirim" placeholder="Pengirim Surat" value="{{ old('pengirim') }}" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_sifat_surat" class="form-label">Sifat Surat</label>
                <select name="sifat_surat" class="form-select" id="add_sifat_surat" required>
                    <option value="">Pilih Sifat Surat</option>
                    <option value="Biasa" {{ old('sifat_surat') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                    <option value="Segera" {{ old('sifat_surat') == 'Segera' ? 'selected' : '' }}>Segera</option>
                    <option value="Penting" {{ old('sifat_surat') == 'Penting' ? 'selected' : '' }}>Penting</option>
                    <option value="Rahasia" {{ old('sifat_surat') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_tujuan_bagian_id" class="form-label">Bagian Tujuan</label>
                <select name="tujuan_bagian_id" class="form-select" id="add_tujuan_bagian_id" required>
                    <option value="">Pilih Bagian</option>
                    @foreach($bagian ?? [] as $bagianItem)
                        <option value="{{ $bagianItem->id }}" {{ old('tujuan_bagian_id') == $bagianItem->id ? 'selected' : '' }}>
                            {{ $bagianItem->nama_bagian }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label for="add_lampiran_pdf" class="form-label">Lampiran Surat Utama (PDF)</label>
                <input type="file" name="lampiran_pdf" class="form-control" id="add_lampiran_pdf" accept="application/pdf" required>
                <div class="form-text">File PDF maksimal 20MB</div>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_lampiran_pendukung" class="form-label">Dokumen Pendukung</label>
                <input type="file" name="lampiran_pendukung[]" class="form-control" id="add_lampiran_pendukung" multiple accept=".zip,.rar,.docx,.xlsx">
                <div class="form-text">File ZIP, RAR, DOCX, XLSX maksimal 20MB</div>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_ringkasan_isi" class="form-label">Ringkasan Isi</label>
                <textarea name="ringkasan_isi" class="form-control" id="add_ringkasan_isi" rows="5" placeholder="Ringkasan isi surat">{{ old('ringkasan_isi') }}</textarea>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" id="add_keterangan" rows="3" placeholder="Keterangan tambahan">{{ old('keterangan') }}</textarea>
                <div class="invalid-feedback"></div>
            </div>
            
            {{-- Integrasi Disposisi --}}
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="buat_disposisi" id="add_buat_disposisi" class="form-check-input" value="1">
                    <label for="add_buat_disposisi" class="form-check-label">
                        <strong>Buat Disposisi</strong>
                    </label>
                </div>
                <div class="form-text">Centang untuk langsung membuat disposisi setelah surat dicatat</div>
            </div>
            
            <div id="add_disposisi_fields" class="mb-3" style="display: none;">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-share-alt me-2"></i>Form Disposisi</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_disposisi_tujuan_bagian" class="form-label">Tujuan Disposisi</label>
                                    <select name="disposisi_tujuan_bagian_id" id="add_disposisi_tujuan_bagian" class="form-select">
                                        <option value="">Pilih Bagian Tujuan</option>
                                        @foreach($bagian as $b)
                                            <option value="{{ $b->id }}">{{ $b->nama_bagian }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_disposisi_status" class="form-label">Status</label>
                                    <select name="disposisi_status" id="add_disposisi_status" class="form-select">
                                        <option value="Menunggu">Menunggu</option>
                                        <option value="Dikerjakan">Dikerjakan</option>
                                        <option value="Selesai">Selesai</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="add_disposisi_instruksi" class="form-label">Instruksi</label>
                            <textarea name="disposisi_instruksi" id="add_disposisi_instruksi" class="form-control" rows="3" placeholder="Instruksi untuk bagian tujuan"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="add_disposisi_catatan" class="form-label">Catatan</label>
                            <textarea name="disposisi_catatan" id="add_disposisi_catatan" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" aria-label="close" data-bs-dismiss="modal" id="addSuratMasukCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="addSuratMasukSubmitBtn">
            Simpan
        </button>
    </div>
</form>

@push('scripts')
<script>
    /**
     * ANCHOR: Add Surat Masuk Handlers
     * Handle the add surat masuk form submission
     */
    const addSuratMasukHandlers = () => {
        const addSuratMasukForm = document.getElementById('addSuratMasukForm');
        const addSuratMasukSubmitBtn = document.getElementById('addSuratMasukSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        // Flag to prevent double submission
        let isSubmitting = false;
        
        if (addSuratMasukForm) {
            addSuratMasukForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Prevent double submission
                if (isSubmitting) {
                    console.log('Form is already being submitted, ignoring...');
                    return;
                }
                isSubmitting = true;
                
                clearErrors(addSuratMasukForm);
                setLoadingState(true, addSuratMasukSubmitBtn);

                try {
                    const formData = new FormData(addSuratMasukForm);
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000);
                    const response = await fetch(addSuratMasukForm.action, {
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
                        
                        // Disable form to prevent further submission
                        addSuratMasukForm.style.pointerEvents = 'none';
                        addSuratMasukSubmitBtn.disabled = true;
                        
                        // Hide modal immediately
                        bootstrap.Modal.getInstance(document.getElementById('modalAddSuratMasuk')).hide();
                        
                        // Reload page
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        handleErrorResponse(data, addSuratMasukForm);
                    }
                } catch (error) {
                    handleErrorResponse(error, addSuratMasukForm);
                } finally {
                    setLoadingState(false, addSuratMasukSubmitBtn);
                    isSubmitting = false; // Reset flag
                }
            });
        }
    }

    /**
     * ANCHOR: Toggle Disposisi Fields
     * Toggle the visibility of disposisi fields based on checkbox
     */
    const toggleDisposisiFields = () => {
        const checkbox = document.getElementById('add_buat_disposisi');
        const fields = document.getElementById('add_disposisi_fields');
        
        if (checkbox && fields) {
            if (checkbox.checked) {
                fields.style.display = 'block';
            } else {
                fields.style.display = 'none';
            }
        }
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetAddSuratMasukFormOnModalClose = () => {
        const modalAddSuratMasuk = document.getElementById('modalAddSuratMasuk');
        const addSuratMasukForm = document.getElementById('addSuratMasukForm');
        
        if (modalAddSuratMasuk && addSuratMasukForm) {
            modalAddSuratMasuk.addEventListener('hidden.bs.modal', function() {
                // Reset form
                addSuratMasukForm.reset();
                
                // Re-enable form
                addSuratMasukForm.style.pointerEvents = 'auto';
                
                // Clear validation errors
                clearErrors(addSuratMasukForm);
                
                // Reset loading state if any
                const addSuratMasukSubmitBtn = document.getElementById('addSuratMasukSubmitBtn');
                setLoadingState(false, addSuratMasukSubmitBtn);
                addSuratMasukSubmitBtn.disabled = false;
                
                // Hide disposisi fields
                const disposisiFields = document.getElementById('add_disposisi_fields');
                if (disposisiFields) {
                    disposisiFields.style.display = 'none';
                }
                
                // Uncheck disposisi checkbox
                const disposisiCheckbox = document.getElementById('add_buat_disposisi');
                if (disposisiCheckbox) {
                    disposisiCheckbox.checked = false;
                }
            });
        }
    }

    // ANCHOR: Initialize add surat masuk handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        addSuratMasukHandlers();
        resetAddSuratMasukFormOnModalClose();
        
        // Add event listener for disposisi checkbox
        const disposisiCheckbox = document.getElementById('add_buat_disposisi');
        if (disposisiCheckbox) {
            disposisiCheckbox.addEventListener('change', toggleDisposisiFields);
        }
    });
</script>
@endpush
