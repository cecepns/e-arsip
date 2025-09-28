<form id="editDisposisiForm">
    <input type="hidden" name="disposisi_id" id="editDisposisiId">
    
    {{-- SECTION: Informasi Disposisi --}}
    <div class="mb-4">
         <h5 class="mb-3 text-primary">
             Edit Disposisi
         </h5>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editTujuanBagian" class="form-label">Tujuan Bagian</label>
                    <select name="tujuan_bagian_id" class="form-select" id="editTujuanBagian" required>
                        <option value="">Pilih Bagian Tujuan</option>
                        @foreach($bagian as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_bagian }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="editStatus" class="form-label">Status Disposisi</label>
                    <select name="status" class="form-select" id="editStatus" required>
                        <option value="">Pilih Status</option>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Dikerjakan">Dikerjakan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="editTanggalDisposisi" class="form-label">Tanggal Disposisi</label>
                    <input type="date" name="tanggal_disposisi" class="form-control" id="editTanggalDisposisi">
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="editBatasWaktu" class="form-label">Batas Waktu</label>
                    <input type="date" name="batas_waktu" class="form-control" id="editBatasWaktu">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editInstruksi" class="form-label">Instruksi Disposisi</label>
                    <textarea name="isi_instruksi" class="form-control" id="editInstruksi" rows="4" placeholder="Masukkan instruksi disposisi..." required></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="editCatatan" class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" id="editCatatan" rows="3" placeholder="Masukkan catatan tambahan..."></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION: Informasi Surat Masuk (Read Only) --}}
    <div class="mb-4">
         <h5 class="mb-3 text-secondary">
             Informasi Surat Masuk
         </h5>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Nomor Surat</label>
                    <input type="text" class="form-control" id="editNomorSurat" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Perihal</label>
                    <input type="text" class="form-control" id="editPerihal" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Disposisi Dari</label>
                    <div class="form-control-plaintext">
                        <div class="d-flex flex-column">
                            <span id="editDisposisiDari" class="fw-semibold text-info">-</span>
                            <small id="editDisposisiDariBagian" class="text-muted">-</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Pengirim</label>
                    <input type="text" class="form-control" id="editPengirim" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sifat Surat</label>
                    <input type="text" class="form-control" id="editSifatSurat" readonly>
                </div>
            </div>
        </div>
    </div>
</form>