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
