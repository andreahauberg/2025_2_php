export function burgerMenu() {
  const burger = document.querySelector(".burger");
  const nav = document.querySelector("nav");

  if (!burger || !nav) return; // safety check

  burger.addEventListener("click", () => {
    // toggle nav
    nav.classList.toggle("active");

    // toggle icon
    burger.classList.toggle("open");
  });
}
