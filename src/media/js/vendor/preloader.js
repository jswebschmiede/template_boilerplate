// File#: _1_preloader

(() => {
    setTimeout(() => {
        const preloader = document.getElementById('loader');
        preloader?.classList.add('done');

        new Promise((resolve) => setTimeout(resolve, 500)).then(() => {
            preloader?.remove();
        });
    }, 500);
})();
