import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    const formsWithLoading = document.querySelectorAll('form[data-disable-on-submit]');

    formsWithLoading.forEach((form) => {
        form.addEventListener('submit', () => {
            form.querySelectorAll('[data-submit-label]').forEach((button) => {
                if (button instanceof HTMLButtonElement) {
                    button.disabled = true;
                    button.dataset.originalLabel = button.textContent ?? '';
                    button.textContent = button.dataset.submitLabel ?? 'Memproses...';
                }
            });
        });
    });

    const tanggalPinjam = document.getElementById('tgl_pinjam');
    const tanggalKembali = document.getElementById('tgl_kembali_rencana');

    if (tanggalPinjam instanceof HTMLInputElement && tanggalKembali instanceof HTMLInputElement) {
        const syncMinTanggalKembali = () => {
            if (!tanggalPinjam.value) {
                tanggalKembali.removeAttribute('min');
                return;
            }

            const pinjamDate = new Date(`${tanggalPinjam.value}T00:00:00`);
            if (Number.isNaN(pinjamDate.getTime())) {
                return;
            }

            pinjamDate.setDate(pinjamDate.getDate() + 1);
            const yyyy = pinjamDate.getFullYear();
            const mm = String(pinjamDate.getMonth() + 1).padStart(2, '0');
            const dd = String(pinjamDate.getDate()).padStart(2, '0');
            const minKembali = `${yyyy}-${mm}-${dd}`;
            tanggalKembali.setAttribute('min', minKembali);

            if (tanggalKembali.value && tanggalKembali.value < minKembali) {
                tanggalKembali.value = minKembali;
            }
        };

        const syncTanggalKembali = () => {
            if (!tanggalPinjam.value || tanggalKembali.value) {
                syncMinTanggalKembali();
                return;
            }

            const sourceDate = new Date(`${tanggalPinjam.value}T00:00:00`);
            if (Number.isNaN(sourceDate.getTime())) {
                return;
            }

            sourceDate.setDate(sourceDate.getDate() + 7);
            const yyyy = sourceDate.getFullYear();
            const mm = String(sourceDate.getMonth() + 1).padStart(2, '0');
            const dd = String(sourceDate.getDate()).padStart(2, '0');
            tanggalKembali.value = `${yyyy}-${mm}-${dd}`;
            syncMinTanggalKembali();
        };

        syncTanggalKembali();
        syncMinTanggalKembali();
        tanggalPinjam.addEventListener('change', syncTanggalKembali);
    }

    const kategoriSingles = document.querySelectorAll('[data-kategori-single]');

    kategoriSingles.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            if (!checkbox.checked) {
                return;
            }

            const form = checkbox.closest('form');
            if (!form) {
                return;
            }

            form.querySelectorAll('[data-kategori-single]').forEach((other) => {
                if (other !== checkbox) {
                    other.checked = false;
                }
            });
        });
    });

    const pickers = document.querySelectorAll('[data-multiselect]');

    pickers.forEach((picker) => {
        const searchInput = picker.querySelector('[data-multiselect-search]');
        const select = picker.querySelector('[data-multiselect-select]');
        const selectVisibleButton = picker.querySelector('[data-multiselect-select-visible]');
        const clearButton = picker.querySelector('[data-multiselect-clear]');
        const summary = picker.querySelector('[data-multiselect-summary]');

        if (!searchInput || !select || !summary) {
            return;
        }

        const label = select.dataset.multiselectLabel ?? 'item';
        const options = Array.from(select.options).filter((option) => option.value !== '');

        const renderSummary = () => {
            const totalVisible = options.filter((option) => !option.hidden).length;
            const totalSelected = options.filter((option) => option.selected).length;
            summary.textContent = `${totalSelected} ${label} dipilih • ${totalVisible} ${label} terlihat`;
        };

        const filterOptions = () => {
            const keyword = searchInput.value.trim().toLowerCase();

            options.forEach((option) => {
                const searchable = option.dataset.search ?? option.text.toLowerCase();
                option.hidden = keyword !== '' && !searchable.includes(keyword);
            });

            renderSummary();
        };

        searchInput.addEventListener('input', filterOptions);

        select.addEventListener('change', renderSummary);

        selectVisibleButton?.addEventListener('click', () => {
            options.forEach((option) => {
                if (!option.hidden) {
                    option.selected = true;
                }
            });
            renderSummary();
        });

        clearButton?.addEventListener('click', () => {
            options.forEach((option) => {
                option.selected = false;
            });
            renderSummary();
        });

        renderSummary();
    });

    const profileCamera = document.querySelector('[data-profile-camera]');

    if (profileCamera) {
        const startButton = profileCamera.querySelector('[data-camera-start]');
        const captureButton = profileCamera.querySelector('[data-camera-capture]');
        const stopButton = profileCamera.querySelector('[data-camera-stop]');
        const video = profileCamera.querySelector('[data-camera-video]');
        const canvas = profileCamera.querySelector('[data-camera-canvas]');
        const fileInput = profileCamera.querySelector('#camera_photo');
        const galleryInput = document.getElementById('profile_photo');
        const preview = document.querySelector('.profile-photo-preview');
        const status = profileCamera.querySelector('[data-camera-status]');
        let stream = null;

        const setStatus = (message) => {
            if (status) {
                status.textContent = message;
            }
        };

        const stopCamera = () => {
            if (stream) {
                stream.getTracks().forEach((track) => track.stop());
                stream = null;
            }

            video?.classList.add('d-none');
            if (startButton instanceof HTMLButtonElement) startButton.disabled = false;
            if (captureButton instanceof HTMLButtonElement) captureButton.disabled = true;
            if (stopButton instanceof HTMLButtonElement) stopButton.disabled = true;
        };

        startButton?.addEventListener('click', async () => {
            if (!navigator.mediaDevices?.getUserMedia || !(video instanceof HTMLVideoElement)) {
                setStatus('Browser ini belum mendukung kamera langsung. Gunakan upload dari galeri.');
                return;
            }

            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user' },
                    audio: false,
                });

                video.srcObject = stream;
                video.classList.remove('d-none');
                if (startButton instanceof HTMLButtonElement) startButton.disabled = true;
                if (captureButton instanceof HTMLButtonElement) captureButton.disabled = false;
                if (stopButton instanceof HTMLButtonElement) stopButton.disabled = false;
                setStatus('Kamera aktif. Posisikan wajah, lalu klik Ambil Foto.');
            } catch (error) {
                setStatus('Kamera tidak bisa dibuka. Pastikan izin kamera diberikan oleh browser.');
            }
        });

        captureButton?.addEventListener('click', () => {
            if (!(video instanceof HTMLVideoElement) || !(canvas instanceof HTMLCanvasElement) || !(fileInput instanceof HTMLInputElement)) {
                return;
            }

            const width = video.videoWidth;
            const height = video.videoHeight;

            if (!width || !height) {
                setStatus('Kamera belum siap. Tunggu sebentar lalu coba lagi.');
                return;
            }

            canvas.width = width;
            canvas.height = height;
            canvas.getContext('2d')?.drawImage(video, 0, 0, width, height);

            canvas.toBlob((blob) => {
                if (!blob) {
                    setStatus('Foto gagal diambil. Coba ulangi.');
                    return;
                }

                const file = new File([blob], `foto-profil-${Date.now()}.jpg`, { type: 'image/jpeg' });
                const transfer = new DataTransfer();
                transfer.items.add(file);
                fileInput.files = transfer.files;

                if (galleryInput instanceof HTMLInputElement) {
                    galleryInput.value = '';
                }

                const objectUrl = URL.createObjectURL(blob);
                if (preview) {
                    preview.innerHTML = `<img src="${objectUrl}" alt="Preview foto profil">`;
                }

                setStatus('Foto sudah diambil. Klik Simpan untuk menyimpan foto profil.');
                stopCamera();
            }, 'image/jpeg', 0.9);
        });

        stopButton?.addEventListener('click', () => {
            stopCamera();
            setStatus('Kamera ditutup.');
        });

        galleryInput?.addEventListener('change', () => {
            if (fileInput instanceof HTMLInputElement) {
                fileInput.value = '';
            }

            stopCamera();
            setStatus('File galeri dipilih. Klik Simpan untuk menyimpan foto profil.');
        });

        profileCamera.closest('form')?.addEventListener('submit', stopCamera);
    }
});
