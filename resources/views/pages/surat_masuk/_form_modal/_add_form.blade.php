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
                <label for="add_ringkasan_isi" class="form-label">Ringkasan Isi</label>
                <textarea name="ringkasan_isi" class="form-control" id="add_ringkasan_isi" rows="4" placeholder="Ringkasan isi surat">{{ old('ringkasan_isi') }}</textarea>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" id="add_keterangan" rows="3" placeholder="Keterangan tambahan">{{ old('keterangan') }}</textarea>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_lampiran_pdf" class="form-label">Lampiran PDF (Wajib)</label>
                <input type="file" name="lampiran_pdf" class="form-control" id="add_lampiran_pdf" accept=".pdf" required>
                <div class="form-text">Format: PDF, Maksimal 20MB</div>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="add_lampiran_pendukung" class="form-label">Dokumen Pendukung (Opsional)</label>
                <input type="file" name="lampiran_pendukung[]" class="form-control" id="add_lampiran_pendukung" accept=".zip,.rar,.docx,.xlsx" multiple>
                <div class="form-text">Format: ZIP, RAR, DOCX, XLSX. Maksimal 20MB per file</div>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="addSuratMasukSubmitBtn">
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <i class="fas fa-save"></i> Simpan
        </button>
    </div>
</form>
