function activateValidation() {
    document.querySelectorAll("[data-rule]").forEach((input) => {
        const pattern = input.getAttribute("data-rule");
        const re = new RegExp(pattern);

        input.addEventListener("input", () => {
            if (re.test(input.value)) {
                input.classList.remove("x-error");
                input.classList.add("x-valid");
            } else {
                input.classList.remove("x-valid");
                input.classList.add("x-error");
            }
        });
    });
}

activateValidation();

document.addEventListener("mix:page-updated", () => {
    console.log("MixHTML updated → validation reactivated");
    activateValidation();
});

document.addEventListener("DOMContentLoaded", () => {
    const openButtons = document.querySelectorAll("[data-open]");
    const closeButtons = document.querySelectorAll(".x-dialog__close");

    openButtons.forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const targetId = btn.getAttribute("data-open");
            const dialog = document.getElementById(targetId);
            if (!dialog) return;

            if (targetId === "updatePostDialog") {
                const postPk = btn.getAttribute("data-post-pk");
                let postMessage = "";
                const postElement = btn.closest(".post");

                if (postElement) {
                    const m = postElement.querySelector(".text");
                    postMessage = m ? m.textContent.trim() : "";
                }

                const pkInput = dialog.querySelector("#postPkInput");
                const msgInput = dialog.querySelector("#postMessageInput");

                if (pkInput) pkInput.value = postPk || "";
                if (msgInput) msgInput.value = postMessage || "";
            }

            dialog.classList.add("active");
        });
    });

    closeButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            const dialog = btn.closest(".x-dialog");
            if (dialog) dialog.classList.remove("active");
        });
    });
    document.querySelectorAll(".x-dialog__overlay").forEach((overlay) => {
        overlay.addEventListener("click", () => {
            const dialog = overlay.closest(".x-dialog");
            if (!dialog) return;

            if (dialog.id === "signupDialog" && dialog.dataset.openState === "open") {
                return;
            }

            dialog.classList.remove("active");
        });
    });
});