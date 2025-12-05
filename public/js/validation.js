console.log("VALIDATION JS LOADED");

document.addEventListener("click", (e) => {
    const dialog = e.target.closest(".x-dialog");
    if (!dialog) return;

    const isOverlay = e.target.classList.contains("x-dialog__overlay");
    if (!isOverlay) return;

    const isSignup = dialog.id === "signupDialog";
    const forcedOpen = dialog.classList.contains("active");

    if (isSignup && forcedOpen) {
        e.stopPropagation();
        e.preventDefault();
        return;
    }
    dialog.classList.remove("active");
});

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
    console.log("MixHTML → reinitializing validation + dialog guard");
    activateValidation();
});