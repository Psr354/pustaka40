<section>
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h2 class="h6 mb-1">Hapus Akun</h2>
            <p class="text-muted small mb-0">
                Akun akan diarsipkan dan tidak bisa dipakai login sampai dipulihkan oleh admin.
            </p>
        </div>
    </div>

    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
        Hapus Akun
    </button>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}" data-disable-on-submit>
                    @csrf
                    @method('delete')

                    <div class="modal-body">
                        <p class="text-muted small">
                            Anda akan logout setelah akun diarsipkan. Masukkan password untuk melanjutkan.
                        </p>

                        <div class="mt-3">
                            <label for="delete_password" class="form-label">Password</label>
                            <input
                                id="delete_password"
                                name="password"
                                type="password"
                                class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                                required
                                placeholder="Masukkan password"
                            />
                            @if ($errors->userDeletion->has('password'))
                                <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" data-submit-label="Menghapus...">Hapus Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modalElement = document.getElementById('deleteAccountModal');
                if (modalElement) {
                    const modalInstance = new bootstrap.Modal(modalElement);
                    modalInstance.show();
                }
            });
        </script>
    @endif
</section>
