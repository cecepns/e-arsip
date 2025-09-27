<form id="resetPasswordForm" action="" method="POST">
    @csrf
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="resetPasswordCancelBtn">Batal</button>
    <button type="submit" class="btn btn-warning" id="resetPasswordSubmitBtn">
        Reset Password
    </button>
    <button type="button" class="btn btn-success" id="resetPasswordCloseBtn" style="display: none;" data-bs-dismiss="modal">
        Tutup
    </button>
</form>
