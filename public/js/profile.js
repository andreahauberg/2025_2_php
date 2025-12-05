document.addEventListener("DOMContentLoaded", () => {

    function showToast(msg) {
        const t = document.createElement("div");
        t.className = "x-toast-error";
        t.innerText = msg;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
    }

    function validateImage(file) {
        const allowed = ["image/jpeg", "image/png", "image/webp"];
        const max = 3 * 1024 * 1024;

        if (!allowed.includes(file.type)) {
            showToast("File type not allowed. Use JPG, PNG or WEBP.");
            return false;
        }
        if (file.size > max) {
            showToast("File is too large (max 3 MB).");
            return false;
        }
        return true;
    }

    const coverInput = document.querySelector('.cover-upload-form input[type="file"]');
    const coverBtn   = document.querySelector('.cover-upload-btn');
    const coverIcon  = document.querySelector('.cover-upload-btn i');

    if (coverInput && coverBtn && coverIcon) {
        coverInput.addEventListener("change", () => {
            if (!coverInput.files.length) return;

            const file = coverInput.files[0];
            if (!validateImage(file)) {
                coverInput.value = "";
                return;
            }

            coverIcon.classList.remove("fa-camera");
            coverIcon.classList.add("fa-check");

            showToast("Cover image ready. Click the icon to save.");

            coverBtn.addEventListener("click", function handler(e) {
                e.preventDefault();
                coverInput.form.submit();
            }, { once: true });
        });
    }

    const avatarInput = document.querySelector('.avatar-edit-btn input[type="file"]');
    const avatarBtn   = document.querySelector('.avatar-edit-btn');
    const avatarIcon  = document.querySelector('.avatar-edit-btn i');

    if (avatarInput && avatarBtn && avatarIcon) {
        avatarInput.addEventListener("change", () => {
            if (!avatarInput.files.length) return;

            const file = avatarInput.files[0];
            if (!validateImage(file)) {
                avatarInput.value = "";
                return;
            }

            avatarIcon.classList.remove("fa-camera");
            avatarIcon.classList.add("fa-check");

            showToast("Profile picture ready. Click the icon to save.");

            avatarBtn.addEventListener("click", function handler(e) {
                e.preventDefault();
                avatarInput.form.submit();
            }, { once: true });
        });
    }

});