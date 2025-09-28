<form id="editSuratMasukForm" action="" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" id="edit_surat_masuk_id">
    
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
            
            {{-- Disposisi yang Sudah Ada --}}
            <div class="mb-3">
                <label class="form-label"><strong>Disposisi yang Sudah Ada</strong></label>
                <div id="edit_existing_disposisi_list" class="border rounded p-3 bg-light">
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="mb-0">Memuat disposisi...</p>
                    </div>
                </div>
            </div>
            
            {{-- Form Edit Disposisi --}}
            <div class="mb-3" id="edit_disposisi_form_section" style="display: none;">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Disposisi</h6>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="disposisi_id" id="edit_disposisi_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_disposisi_tujuan_bagian" class="form-label">Tujuan Disposisi</label>
                                    <select name="disposisi_tujuan_bagian_id" id="edit_disposisi_tujuan_bagian" class="form-select">
                                        <option value="">Pilih Bagian Tujuan</option>
                                        @foreach($bagian ?? [] as $b)
                                            <option value="{{ $b->id }}">{{ $b->nama_bagian }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_disposisi_status" class="form-label">Status</label>
                                    <select name="disposisi_status" id="edit_disposisi_status" class="form-select">
                                        <option value="Menunggu">Menunggu</option>
                                        <option value="Dikerjakan">Dikerjakan</option>
                                        <option value="Selesai">Selesai</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_disposisi_instruksi" class="form-label">Instruksi</label>
                            <textarea name="disposisi_instruksi" id="edit_disposisi_instruksi" class="form-control" rows="3" placeholder="Instruksi untuk bagian tujuan"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_disposisi_catatan" class="form-label">Catatan</label>
                            <textarea name="disposisi_catatan" id="edit_disposisi_catatan" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary btn-sm" id="cancel_edit_disposisi">Batal</button>
                            <button type="button" class="btn btn-warning btn-sm" id="update_disposisi">Update Disposisi</button>
                        </div>
                    </div>
                </div>
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
     * ANCHOR: Show Edit Surat Masuk Modal
     * Show the edit surat masuk modal
     * @param {number} suratMasukId - The id of the surat masuk to edit
     */
    const showEditSuratMasukModal = async (suratMasukId) => {
        const editSuratMasukForm = document.getElementById('editSuratMasukForm');
        const idInput = document.getElementById('edit_surat_masuk_id');
        const nomorSuratInput = document.getElementById('edit_nomor_surat');
        const tanggalSuratInput = document.getElementById('edit_tanggal_surat');
        const tanggalTerimaInput = document.getElementById('edit_tanggal_terima');
        const perihalInput = document.getElementById('edit_perihal');
        const pengirimInput = document.getElementById('edit_pengirim');
        const sifatSuratInput = document.getElementById('edit_sifat_surat');
        const tujuanBagianInput = document.getElementById('edit_tujuan_bagian_id');
        const ringkasanIsiInput = document.getElementById('edit_ringkasan_isi');
        const keteranganInput = document.getElementById('edit_keterangan');

        const suratMasuk = suratMasukDataCurrentPage.data.find(surat => surat.id === suratMasukId);
        const { id, nomor_surat, tanggal_surat, tanggal_terima, perihal, pengirim, sifat_surat, tujuan_bagian_id, ringkasan_isi, keterangan } = suratMasuk;

        const formatDateForInput = (isoDate) => {
            if (!isoDate) return '';
            return new Date(isoDate).toISOString().split('T')[0];
        };

        idInput.value = id;
        nomorSuratInput.value = nomor_surat || '';
        tanggalSuratInput.value = formatDateForInput(tanggal_surat) || '';
        tanggalTerimaInput.value = formatDateForInput(tanggal_terima) || '';
        perihalInput.value = perihal || '';
        pengirimInput.value = pengirim || '';
        sifatSuratInput.value = sifat_surat || '';
        tujuanBagianInput.value = tujuan_bagian_id || '';
        ringkasanIsiInput.value = ringkasan_isi || '';
        keteranganInput.value = keterangan || '';

        // Load disposisi data
        await loadDisposisiForEdit(id);

        editSuratMasukForm.action = `/surat-masuk/${id}`;
    }

    /**
     * ANCHOR: Load Disposisi for Edit
     * Load disposisi data for the surat masuk
     * @param {number} suratMasukId - The id of the surat masuk
     */
    const loadDisposisiForEdit = async (suratMasukId) => {
        const disposisiList = document.getElementById('edit_existing_disposisi_list');
        
        try {
            const csrfToken = (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                document.querySelector('input[name="_token"]')?.value
            );

            const response = await fetch(`/surat-masuk/${suratMasukId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch disposisi data');
            }

            const data = await response.json();
            
            if (data.success && data.suratMasuk.disposisi) {
                populateDisposisiList(data.suratMasuk.disposisi);
            } else {
                disposisiList.innerHTML = `
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">Tidak ada disposisi</p>
                    </div>
                `;
            }

        } catch (error) {
            console.error('Error loading disposisi:', error);
            disposisiList.innerHTML = `
                <div class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p class="mb-0">Gagal memuat disposisi</p>
                    <small class="text-muted">${error.message}</small>
                </div>
            `;
        }
    }

    /**
     * ANCHOR: Populate Disposisi List
     * Populate the disposisi list with existing disposisi
     * @param {Array} disposisi - Array of disposisi data
     */
    const populateDisposisiList = (disposisi) => {
        const disposisiList = document.getElementById('edit_existing_disposisi_list');
        
        if (disposisi.length === 0) {
            disposisiList.innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">Tidak ada disposisi</p>
                </div>
            `;
            return;
        }

        let disposisiHtml = '<div class="row">';
        
        disposisi.forEach((disp, index) => {
            const statusBadgeClass = 
                disp.status === 'Menunggu' ? 'bg-warning' :
                disp.status === 'Dikerjakan' ? 'bg-info' :
                disp.status === 'Selesai' ? 'bg-success' : 'bg-secondary';
            
            disposisiHtml += `
                <div class="col-md-6 mb-3">
                    <div class="card border">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">Disposisi ${index + 1}</h6>
                                <span class="badge ${statusBadgeClass}">${disp.status}</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Tujuan:</small>
                                <p class="mb-1 fw-semibold">${disp.tujuan_bagian?.nama_bagian || '-'}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Instruksi:</small>
                                <p class="mb-1">${disp.isi_instruksi || '-'}</p>
                            </div>
                            ${disp.catatan ? `
                                <div class="mb-2">
                                    <small class="text-muted">Catatan:</small>
                                    <p class="mb-1">${disp.catatan}</p>
                                </div>
                            ` : ''}
                            <div class="text-muted mb-2">
                                <small>Dibuat: ${disp.created_at ? new Date(disp.created_at).toLocaleString('id-ID') : '-'}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-warning btn-sm" onclick="editDisposisi(${disp.id})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteDisposisi(${disp.id})">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        disposisiHtml += '</div>';
        disposisiList.innerHTML = disposisiHtml;
    }

    /**
     * ANCHOR: Edit Disposisi
     * Edit a specific disposisi
     * @param {number} disposisiId - The id of the disposisi to edit
     */
    window.editDisposisi = async (disposisiId) => {
        try {
            const csrfToken = (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                document.querySelector('input[name="_token"]')?.value
            );

            const response = await fetch(`/disposisi/${disposisiId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch disposisi data');
            }

            const data = await response.json();
            
            if (data.success) {
                populateEditDisposisiForm(data.disposisi);
                document.getElementById('edit_disposisi_form_section').style.display = 'block';
            } else {
                showToast('Gagal memuat data disposisi', 'error');
            }

        } catch (error) {
            console.error('Error loading disposisi:', error);
            showToast('Gagal memuat data disposisi', 'error');
        }
    }

    /**
     * ANCHOR: Populate Edit Disposisi Form
     * Populate the edit disposisi form with data
     * @param {Object} disposisi - The disposisi data
     */
    const populateEditDisposisiForm = (disposisi) => {
        document.getElementById('edit_disposisi_id').value = disposisi.id;
        document.getElementById('edit_disposisi_tujuan_bagian').value = disposisi.tujuan_bagian_id;
        document.getElementById('edit_disposisi_status').value = disposisi.status;
        document.getElementById('edit_disposisi_instruksi').value = disposisi.isi_instruksi || '';
        document.getElementById('edit_disposisi_catatan').value = disposisi.catatan || '';
    }

    /**
     * ANCHOR: Update Disposisi
     * Update the disposisi
     */
    const updateDisposisi = async () => {
        const disposisiId = document.getElementById('edit_disposisi_id').value;
        const formData = {
            tujuan_bagian_id: document.getElementById('edit_disposisi_tujuan_bagian').value,
            status: document.getElementById('edit_disposisi_status').value,
            isi_instruksi: document.getElementById('edit_disposisi_instruksi').value,
            catatan: document.getElementById('edit_disposisi_catatan').value,
        };

        try {
            const csrfToken = (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                document.querySelector('input[name="_token"]')?.value
            );

            const response = await fetch(`/disposisi/${disposisiId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                showToast(data.message, 'success');
                document.getElementById('edit_disposisi_form_section').style.display = 'none';
                // Reload disposisi list
                const suratMasukId = document.getElementById('edit_surat_masuk_id').value;
                await loadDisposisiForEdit(suratMasukId);
            } else {
                showToast(data.message || 'Gagal memperbarui disposisi', 'error');
            }

        } catch (error) {
            console.error('Error updating disposisi:', error);
            showToast('Gagal memperbarui disposisi', 'error');
        }
    }

    /**
     * ANCHOR: Delete Disposisi
     * Delete a specific disposisi
     * @param {number} disposisiId - The id of the disposisi to delete
     */
    window.deleteDisposisi = async (disposisiId) => {
        if (!confirm('Apakah Anda yakin ingin menghapus disposisi ini?')) {
            return;
        }

        try {
            const csrfToken = (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                document.querySelector('input[name="_token"]')?.value
            );

            const response = await fetch(`/disposisi/${disposisiId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                showToast(data.message, 'success');
                // Reload disposisi list
                const suratMasukId = document.getElementById('edit_surat_masuk_id').value;
                await loadDisposisiForEdit(suratMasukId);
            } else {
                showToast(data.message || 'Gagal menghapus disposisi', 'error');
            }

        } catch (error) {
            console.error('Error deleting disposisi:', error);
            showToast('Gagal menghapus disposisi', 'error');
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
                
                // Hide disposisi edit form
                const disposisiFormSection = document.getElementById('edit_disposisi_form_section');
                if (disposisiFormSection) {
                    disposisiFormSection.style.display = 'none';
                }
            });
        }
    }

    // ANCHOR: Initialize edit surat masuk handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        editSuratMasukHandlers();
        resetEditSuratMasukFormOnModalClose();
        
        // Add event listeners for disposisi buttons
        const updateDisposisiBtn = document.getElementById('update_disposisi');
        const cancelEditDisposisiBtn = document.getElementById('cancel_edit_disposisi');
        
        if (updateDisposisiBtn) {
            updateDisposisiBtn.addEventListener('click', updateDisposisi);
        }
        
        if (cancelEditDisposisiBtn) {
            cancelEditDisposisiBtn.addEventListener('click', function() {
                document.getElementById('edit_disposisi_form_section').style.display = 'none';
            });
        }
    });
</script>
@endpush
