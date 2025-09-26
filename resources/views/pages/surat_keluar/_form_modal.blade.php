<form method="POST" class="p-2" enctype="multipart/form-data" id="formSuratKeluar">
    @csrf
    <input type="hidden" name="surat_keluar_id" id="surat_keluar_id">
    <input type="hidden" name="_method" id="formMethod" value="POST">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label">Nomor Surat</label>
                <input type="text" name="nomor_surat" id="nomor_surat" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Tanggal Surat</label>
                <input type="date" name="tanggal_surat" id="tanggal_surat" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Tanggal Keluar</label>
                <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Perihal</label>
                <input type="text" name="perihal" id="perihal" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Penerima</label>
                <input type="text" name="tujuan" id="tujuan" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Bagian Pengirim</label>
                <select name="pengirim_bagian_id" id="pengirim_bagian_id" class="form-control" required>
                    <option value="">- Pilih Bagian -</option>
                    @foreach($bagian as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_bagian }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label">Lampiran Surat Utama (PDF)</label>
                <input type="file" name="lampiran_pdf" id="lampiran_pdf" class="form-control" accept="application/pdf" required>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Dokumen Pendukung (ZIP, RAR, DOCX, XLSX)</label>
                <input type="file" name="lampiran_pendukung[]" id="lampiran_pendukung" class="form-control" multiple accept=".zip,.rar,.docx,.xlsx">
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Ringkasan Isi</label>
                <textarea name="ringkasan_isi" rows="5" id="ringkasan_isi" class="form-control" required></textarea>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" rows="5" id="keterangan" class="form-control"></textarea>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" aria-label="Close">Batal</button>
        <button type="submit" id="submitBtn" class="btn btn-primary">Simpan</button>
    </div>
</form>