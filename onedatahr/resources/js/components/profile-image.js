import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

export function initProfileImage() {
    const fileInput = document.querySelector('input[name="photo"]');
    if (!fileInput) return;

    const previewImg = document.querySelector('#profile-photo-preview');
    const cropBtn = document.querySelector('#profile-photo-crop');
    const resetBtn = document.querySelector('#profile-photo-reset');

    const cropperModal = document.getElementById('profile-cropper-modal');
    const cropperImg = document.getElementById('profile-cropper-img');
    const zoomInBtn = document.getElementById('cropper-zoom-in');
    const zoomOutBtn = document.getElementById('cropper-zoom-out');
    const rotateLeftBtn = document.getElementById('cropper-rotate-left');
    const rotateRightBtn = document.getElementById('cropper-rotate-right');
    const cancelBtn = document.getElementById('cropper-cancel');
    const doneBtn = document.getElementById('cropper-done');

    let currentFile = null;
    let cropper = null;

    function dataURLToFile(dataurl, filename) {
        let arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1], bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while (n--) { u8arr[n] = bstr.charCodeAt(n); }
        return new File([u8arr], filename, { type: mime });
    }

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        currentFile = file;
        const reader = new FileReader();
        reader.onload = function (ev) {
            if (previewImg) {
                previewImg.src = ev.target.result;
                previewImg.dataset.original = ev.target.result;
            }
            if (cropBtn) cropBtn.disabled = false;
            if (resetBtn) resetBtn.disabled = false;
        };
        reader.readAsDataURL(file);
    });

    // Open cropper modal when clicking Crop
    if (cropBtn) {
        cropBtn.addEventListener('click', () => {
            if (!currentFile && (!previewImg || !previewImg.src)) return alert('No image to crop');
            // if cropper elements missing, fallback to center crop (old behavior)
            if (!cropperModal || !cropperImg || !doneBtn) {
                // fallback: center-crop like before
                const img = new Image();
                img.src = previewImg.src;
                img.onload = function () {
                    const size = Math.min(img.naturalWidth, img.naturalHeight);
                    const sx = Math.floor((img.naturalWidth - size) / 2);
                    const sy = Math.floor((img.naturalHeight - size) / 2);
                    const canvas = document.createElement('canvas');
                    canvas.width = 1024; canvas.height = 1024;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, sx, sy, size, size, 0, 0, canvas.width, canvas.height);
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                    const croppedFile = dataURLToFile(dataUrl, 'profile-cropped.jpg');
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    fileInput.files = dataTransfer.files;
                    if (previewImg) previewImg.src = dataUrl;
                    alert('Gambar sudah dicrop (center-crop), tekan Save Changes untuk menyimpan.');
                };
                return;
            }

            // show modal (use flex so centering works)
            cropperModal.classList.remove('hidden');
            cropperModal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            // set image src for cropper and initialize when loaded
            const src = previewImg && previewImg.src ? previewImg.src : URL.createObjectURL(currentFile);
            cropperImg.src = src;

            // initialize cropper after image load (ensures dimensions available and visible)
            cropperImg.onload = function () {
                try {
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(cropperImg, {
                        aspectRatio: 1,
                        viewMode: 1,
                        background: false,
                        autoCropArea: 0.9,
                        movable: true,
                        zoomable: true,
                        scalable: true,
                        responsive: true,
                    });
                } catch (err) {
                    console.error('Cropper init failed', err);
                    alert('Gagal memulai cropper, menggunakan fallback.');
                    // hide modal
                    if (cropper) { cropper.destroy(); cropper = null; }
                    cropperModal.classList.remove('flex');
                    cropperModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            };
        });
    }

    // Zoom / rotate / cancel / done handlers
    if (zoomInBtn) zoomInBtn.addEventListener('click', () => { if (cropper) cropper.zoom(0.1); });
    if (zoomOutBtn) zoomOutBtn.addEventListener('click', () => { if (cropper) cropper.zoom(-0.1); });
    if (rotateLeftBtn) rotateLeftBtn.addEventListener('click', () => { if (cropper) cropper.rotate(-45); });
    if (rotateRightBtn) rotateRightBtn.addEventListener('click', () => { if (cropper) cropper.rotate(45); });
    if (cancelBtn) cancelBtn.addEventListener('click', () => {
        if (cropper) { cropper.destroy(); cropper = null; }
        // hide modal and restore scrolling
        cropperModal.classList.remove('flex');
        cropperModal.classList.add('hidden');
        document.body.style.overflow = '';
        // remove image onload handler
        if (cropperImg) { cropperImg.onload = null; }
    });

    if (doneBtn) doneBtn.addEventListener('click', async () => {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas({ width: 1024, height: 1024, imageSmoothingQuality: 'high' });
        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

        // replace file input with cropped file
        const blob = await (await fetch(dataUrl)).blob();
        const file = new File([blob], 'profile-cropped.jpg', { type: 'image/jpeg' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;

        if (previewImg) previewImg.src = dataUrl;

        // clean up
        cropper.destroy();
        cropper = null;
        // hide modal and restore scrolling
        cropperModal.classList.remove('flex');
        cropperModal.classList.add('hidden');
        document.body.style.overflow = '';
        if (cropperImg) { cropperImg.onload = null; }

        // disable crop button (prevent re-crop until user changes file)
        if (cropBtn) cropBtn.disabled = true;
        alert('Crop selesai. Tekan Save Changes untuk menyimpan.');
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            fileInput.value = '';
            if (previewImg) {
                const original = previewImg.dataset.original;
                previewImg.src = original || '';
            }
            if (cropBtn) cropBtn.disabled = true;
            resetBtn.disabled = true;
            currentFile = null;
            if (cropper) { cropper.destroy(); cropper = null; }
            // hide cropper modal and restore scroll if open
            if (cropperModal) {
                cropperModal.classList.remove('flex');
                cropperModal.classList.add('hidden');
            }
            document.body.style.overflow = '';
            if (cropperImg) { cropperImg.onload = null; }
        });
    }
}
