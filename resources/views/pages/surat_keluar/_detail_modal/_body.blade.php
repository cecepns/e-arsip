{{-- SECTION: Informasi Surat Keluar --}}
<div class="mb-4">
    <h5 class="mb-3 text-primary">
        <i class="fas fa-file-export me-2"></i>Informasi Surat Keluar
    </h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <p class="mb-2">
                    <span class="fw-semibold text-dark">Nomor Surat:</span>
                    <span id="detail-nomor-surat" class="text-primary">-</span>
                </p>
                <p class="mb-2">
                    <span class="fw-semibold text-dark">Tanggal Surat:</span>
                    <span id="detail-tanggal-surat">-</span>
                </p>
                <p class="mb-2">
                    <span class="fw-semibold text-dark">Tanggal Keluar:</span>
                    <span id="detail-tanggal-keluar">-</span>
                </p>
                <p class="mb-2">
                    <span class="fw-semibold text-dark">Perihal:</span>
                    <span id="detail-perihal" class="text-dark">-</span>
                </p>
                <p class="mb-2">
                    <span class="fw-semibold text-dark">Sifat Surat:</span>
                    <span id="detail-sifat-surat">-</span>
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <p class="mb-2">
                    <span class="fw-semibold text-dark">Penerima:</span>
                    <span id="detail-tujuan" class="text-dark">-</span>
                </p>
                <p class="mb-2">
                    <span class="fw-semibold text-dark">Bagian Pengirim:</span>
                    <span id="detail-bagian-pengirim" class="text-info">-</span>
                </p>
                <p class="mb-2">
                    <span class="fw-semibold text-dark">Dibuat Oleh:</span>
                    <span id="detail-user" class="text-secondary">-</span>
                </p>
                <p class="mb-2">
                    <span class="fw-semibold text-dark">Dibuat Pada:</span>
                    <span id="detail-created-at" class="text-muted">-</span>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- SECTION: Ringkasan Isi --}}
<div class="mb-4" id="detail-ringkasan-section" style="display: none;">
    <h5 class="mb-3 text-primary">
        <i class="fas fa-align-left me-2"></i>Ringkasan Isi
    </h5>
    <div class="bg-light p-3 rounded">
        <p id="detail-ringkasan-isi" class="mb-0 text-dark">-</p>
    </div>
</div>

{{-- SECTION: Keterangan --}}
<div class="mb-4" id="detail-keterangan-section" style="display: none;">
    <h5 class="mb-3 text-primary">
        <i class="fas fa-sticky-note me-2"></i>Keterangan
    </h5>
    <div class="bg-light p-3 rounded">
        <p id="detail-keterangan" class="mb-0 text-dark">-</p>
    </div>
</div>

{{-- SECTION: Lampiran --}}
<div class="mb-4" id="detail-lampiran-section">
    <h5 class="mb-3 text-primary">
        <i class="fas fa-paperclip me-2"></i>Lampiran
    </h5>
    <div id="detail-lampiran-content">
        <div class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
            <p>Memuat lampiran...</p>
        </div>
    </div>
</div>