<form id="editSuratKeluarForm" action="" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="surat_keluar_id" id="edit_surat_keluar_id" value="">
    
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
                <label for="edit_tanggal_keluar" class="form-label">Tanggal Keluar</label>
                <input type="date" name="tanggal_keluar" class="form-control" id="edit_tanggal_keluar" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="edit_perihal" class="form-label">Perihal</label>
                <input type="text" name="perihal" class="form-control" id="edit_perihal" placeholder="Perihal Surat" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="edit_tujuan" class="form-label">Penerima</label>
                <input type="text" name="tujuan" class="form-control" id="edit_tujuan" placeholder="Penerima Surat" required>
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
                <label for="edit_pengirim_bagian_id" class="form-label">Bagian Pengirim</label>
                <select name="pengirim_bagian_id" class="form-select" id="edit_pengirim_bagian_id" required>
                    <option value="">Pilih Bagian</option>
                    @foreach($bagian ?? [] as $bagianItem)
                        <option value="{{ $bagianItem->id }}">
                            {{ $bagianItem->nama_bagian }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label for="edit_lampiran_pdf" class="form-label">Lampiran Surat Utama (PDF)</label>
                <input type="file" name="lampiran_pdf" class="form-control" id="edit_lampiran_pdf" accept="application/pdf">
                <div class="form-text">Kosongkan jika tidak ingin mengubah file PDF</div>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="edit_lampiran_pendukung" class="form-label">Dokumen Pendukung</label>
                <input type="file" name="lampiran_pendukung[]" class="form-control" id="edit_lampiran_pendukung" multiple accept=".zip,.rar,.docx,.xlsx">
                <div class="form-text">File baru akan ditambahkan ke dokumen pendukung yang sudah ada</div>
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
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" aria-label="close" data-bs-dismiss="modal" id="editSuratKeluarCancelBtn">Batal</button>
        <button type="submit" class="btn btn-primary" id="editSuratKeluarSubmitBtn">Update</button>
    </div>
</form>

@push('scripts')
<script>
    /**
     * ANCHOR: Show Edit Surat Keluar Modal
     * Show the edit surat keluar modal and populate with data
     * @param {number} suratKeluarId - The id of the surat keluar to edit
     */
    const showEditSuratKeluarModal = (suratKeluarId) => {
        const editSuratKeluarForm = document.getElementById('editSuratKeluarForm');
        const idInput = document.getElementById('edit_surat_keluar_id');
        const nomorSuratInput = document.getElementById('edit_nomor_surat');
        const tanggalSuratInput = document.getElementById('edit_tanggal_surat');
        const tanggalKeluarInput = document.getElementById('edit_tanggal_keluar');
        const perihalInput = document.getElementById('edit_perihal');
        const tujuanInput = document.getElementById('edit_tujuan');
        const sifatSuratInput = document.getElementById('edit_sifat_surat');
        const pengirimBagianInput = document.getElementById('edit_pengirim_bagian_id');
        const ringkasanIsiInput = document.getElementById('edit_ringkasan_isi');
        const keteranganInput = document.getElementById('edit_keterangan');

        const suratKeluar = suratKeluarDataCurrentPage.find(surat => surat.id === suratKeluarId);
        const { id, nomor_surat, tanggal_surat, tanggal_keluar, perihal, tujuan, sifat_surat, pengirim_bagian_id, ringkasan_isi, keterangan } = suratKeluar;

        const formatDateForInput = (isoDate) => {
            if (!isoDate) return '';
            return new Date(isoDate).toISOString().split('T')[0];
        };

        idInput.value = id;
        nomorSuratInput.value = nomor_surat || '';
        tanggalSuratInput.value = formatDateForInput(tanggal_surat) || '';
        tanggalKeluarInput.value = formatDateForInput(tanggal_keluar) || '';
        perihalInput.value = perihal || '';
        tujuanInput.value = tujuan || '';
        sifatSuratInput.value = sifat_surat || '';
        pengirimBagianInput.value = pengirim_bagian_id || '';
        ringkasanIsiInput.value = ringkasan_isi || '';
        keteranganInput.value = keterangan || '';

        editSuratKeluarForm.action = `/surat-keluar/${id}`;
    }

    /**
     * ANCHOR: Edit Surat Keluar Handlers
     * Handle the edit surat keluar form submission
     */
    const editSuratKeluarHandlers = () => {
        const editSuratKeluarForm = document.getElementById('editSuratKeluarForm');
        const editSuratKeluarSubmitBtn = document.getElementById('editSuratKeluarSubmitBtn');
        const editSuratKeluarCancelBtn = document.getElementById('editSuratKeluarCancelBtn');
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
            document.querySelector('input[name="_token"]')?.value
        );
        
        editSuratKeluarForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(editSuratKeluarForm);
            setLoadingState(true, editSuratKeluarSubmitBtn);

            try {
                const formData = new FormData(editSuratKeluarForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);
                const response = await fetchWithRetry(editSuratKeluarForm.action, {
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
                    editSuratKeluarForm.reset();
                    bootstrap.Modal.getInstance(document.getElementById('modalEditSuratKeluar')).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleErrorResponse(data, editSuratKeluarForm);
                }
            } catch (error) {
                handleErrorResponse(error, editSuratKeluarForm);
            } finally {
                setLoadingState(false, editSuratKeluarSubmitBtn);
            }
        });
    }

    /**
     * ANCHOR: Reset Form on Modal Close
     * Reset form and clear errors when modal is closed
     */
    const resetEditSuratKeluarFormOnModalClose = () => {
        const modalEditSuratKeluar = document.getElementById('modalEditSuratKeluar');
        const editSuratKeluarForm = document.getElementById('editSuratKeluarForm');
        
        modalEditSuratKeluar.addEventListener('hidden.bs.modal', function() {
            // Reset form
            editSuratKeluarForm.reset();
            
            // Clear validation errors
            clearErrors(editSuratKeluarForm);
            
            // Reset loading state if any
            const editSuratKeluarSubmitBtn = document.getElementById('editSuratKeluarSubmitBtn');
            setLoadingState(false, editSuratKeluarSubmitBtn);
        });
    }

    // ANCHOR: Initialize edit surat keluar handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        editSuratKeluarHandlers();
        resetEditSuratKeluarFormOnModalClose();
    });
</script>
@endpush
