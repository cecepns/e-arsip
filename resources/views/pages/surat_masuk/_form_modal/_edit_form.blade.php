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
                <label for="edit_ringkasan_isi" class="form-label">Ringkasan Isi</label>
                <textarea name="ringkasan_isi" class="form-control" id="edit_ringkasan_isi" rows="4" placeholder="Ringkasan isi surat"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="edit_keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" id="edit_keterangan" rows="3" placeholder="Keterangan tambahan"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="edit_lampiran_pdf" class="form-label">Lampiran PDF (Opsional)</label>
                <input type="file" name="lampiran_pdf" class="form-control" id="edit_lampiran_pdf" accept=".pdf">
                <div class="form-text">Format: PDF, Maksimal 20MB. Kosongkan jika tidak ingin mengubah</div>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="edit_lampiran_pendukung" class="form-label">Dokumen Pendukung (Opsional)</label>
                <input type="file" name="lampiran_pendukung[]" class="form-control" id="edit_lampiran_pendukung" accept=".zip,.rar,.docx,.xlsx" multiple>
                <div class="form-text">Format: ZIP, RAR, DOCX, XLSX. Maksimal 20MB per file</div>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="editSuratMasukSubmitBtn">
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <i class="fas fa-save"></i> Update
        </button>
    </div>
</form>
