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
        <button type="button" class="btn btn-secondary me-2" aria-label="close" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="editSuratKeluarSubmitBtn">Update</button>
    </div>
</form>
