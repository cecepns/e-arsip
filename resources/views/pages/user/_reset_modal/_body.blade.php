<div class="text-center">
    <i class="fas fa-key text-warning mb-3" style="font-size: 3rem;"></i>
    <h5>Reset Password User</h5>
    <p class="text-muted">Apakah Anda yakin ingin mereset password untuk user <strong id="resetUserName"></strong>?</p>
    <p class="text-muted small">Password baru akan digenerate secara otomatis dan ditampilkan setelah reset berhasil.</p>
    
    <!-- Password Result Section (hidden initially) -->
    <div id="passwordResult" class="mt-4" style="display: none;">
        <div class="alert alert-success">
            <h6 class="alert-heading">
                <i class="fas fa-check-circle me-2"></i>Password Berhasil Direset!
            </h6>
            <hr>
            <div class="mb-3">
                <label class="form-label fw-bold">Password Baru:</label>
                <div class="input-group">
                    <input type="text" class="form-control text-center fw-bold" id="newPasswordDisplay" readonly style="font-family: 'Courier New', monospace; font-size: 1.1em;">
                    <button type="button" class="btn btn-outline-secondary" onclick="copyPassword()" title="Copy password">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <p class="mb-0 small text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Silakan berikan password ini kepada user dan minta mereka untuk login dengan password baru.
            </p>
        </div>
    </div>
</div>
