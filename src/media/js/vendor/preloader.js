// File#: _1_preloader

(function () {
  setTimeout(() => {
    const preloader = document.getElementById("loader");
    preloader?.classList.add("done");

    new Promise((resolve) => setTimeout(resolve, 500)).then(() => {
      preloader?.remove();
      document
        .querySelector("[data-animate-headline]")
        ?.classList.add("animate__animated", "animate__fadeInDown");
      document
        .querySelector("[data-animate-scroll]")
        ?.classList.add("animate__animated", "animate__fadeInUp");
    });
  }, 500);
})();
