export function flipBtn() {
    document.querySelectorAll(".flip-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        const obj = this.querySelector("i");
        if (obj.classList.contains("fa-regular")) {
          obj.classList.remove("fa-regular");
          obj.classList.add("fa-solid");
        } else {
          obj.classList.remove("fa-solid");
          obj.classList.add("fa-regular");
        }
      });
    });

}