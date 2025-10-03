<form id="addSuratKeluarForm" action="{{ route('surat_keluar.store') }}" method="POST" enctype="multipart/form-data">
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
                <label for="add_tanggal_keluar" class="form-label">Tanggal Keluar</label>
                <input type="date" name="tanggal_keluar" class="form-control" id="add_tanggal_keluar" value="{{ old('tanggal_keluar') }}" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_perihal" class="form-label">Perihal</label>
                <input type="text" name="perihal" class="form-control" id="add_perihal" placeholder="Perihal Surat" value="{{ old('perihal') }}" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_tujuan" class="form-label">Penerima</label>
                <input type="text" name="tujuan" class="form-control" id="add_tujuan" placeholder="Penerima Surat" value="{{ old('tujuan') }}" required>
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
                @if(Auth::user()->role === 'Admin')
                <label for="add_pengirim_bagian_id" class="form-label">Bagian Pengirim</label>
                <select name="pengirim_bagian_id" class="form-select" id="add_pengirim_bagian_id" required>
                    <option value="">Pilih Bagian</option>
                    @foreach($bagian ?? [] as $bagianItem)
                        <option value="{{ $bagianItem->id }}" {{ old('pengirim_bagian_id') == $bagianItem->id ? 'selected' : '' }}>
                            {{ $bagianItem->nama_bagian }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
                @else
                {{-- Hidden input untuk Staff - auto-set ke bagian mereka --}}
                <input type="hidden" name="pengirim_bagian_id" value="{{ Auth::user()->bagian_id }}">
                <label class="form-label">Bagian Pengirim</label>
                <div class="form-control-plaintext bg-light p-2 rounded">
                    <i class="fas fa-building me-2"></i>
                    {{ Auth::user()->bagian->nama_bagian ?? 'Bagian tidak ditemukan' }}
                </div>
                <div class="form-text">Surat akan otomatis dikirim dari bagian Anda</div>
                @endif
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
        </div>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" aria-label="close" data-bs-dismiss="modal" id="addSuratKeluarCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="addSuratKeluarSubmitBtn">
            Simpan
        </button>
    </div>
</form>

@push('scripts')
<script>
    /**
     * ANCHOR: Add Surat Keluar Handlers
     * Handle the add surat keluar form submission
     */
    const addSuratKeluarHandlers = () => {
        const addSuratKeluarForm = document.getElementById('addSuratKeluarForm');
        const addSuratKeluarSubmitBtn = document.getElementById('addSuratKeluarSubmitBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        if (addSuratKeluarForm) {
            addSuratKeluarForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors(addSuratKeluarForm);
                setLoadingState(true, addSuratKeluarSubmitBtn);

                try {
                    const formData = new FormData(addSuratKeluarForm);
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000);
                    const response = await fetchWithRetry(addSuratKeluarForm.action, {
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
                        addSuratKeluarForm.reset();
                        bootstrap.Modal.getInstance(document.getElementById('modalAddSuratKeluar')).hide();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        handleErrorResponse(data, addSuratKeluarForm);
                    }
                } catch (error) {
                    handleErrorResponse(error, addSuratKeluarForm);
                } finally {
                    setLoadingState(false, addSuratKeluarSubmitBtn);
                }
            });
        }
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetAddSuratKeluarFormOnModalClose = () => {
        const modalAddSuratKeluar = document.getElementById('modalAddSuratKeluar');
        const addSuratKeluarForm = document.getElementById('addSuratKeluarForm');
        
        modalAddSuratKeluar.addEventListener('hidden.bs.modal', function() {
            // Reset form
            addSuratKeluarForm.reset();
            
            // Clear validation errors
            clearErrors(addSuratKeluarForm);
            
            // Reset loading state if any
            const addSuratKeluarSubmitBtn = document.getElementById('addSuratKeluarSubmitBtn');
            setLoadingState(false, addSuratKeluarSubmitBtn);
        });
    }

    // ANCHOR: Initialize add surat keluar handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        addSuratKeluarHandlers();
        resetAddSuratKeluarFormOnModalClose();
    });
</script>
@endpush