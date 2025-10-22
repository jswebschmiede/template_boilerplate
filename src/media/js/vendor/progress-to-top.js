(() => {
    class ProgressToTop {
        constructor(element) {
            this.element = element;
            this.progressPath = this.element.querySelector(".svg-content path");
            this.totalLength = this.progressPath.getTotalLength();
            this.setupProgress();
            this.init();
        }

        init = () => {
            this.setupProgress();
            this.setupEventListeners();
        };

        setupProgress = () => {
            this.progressPath.style.transition = "none";
            this.progressPath.style.strokeDasharray = `${this.totalLength} ${this.totalLength}`;
            this.progressPath.style.strokeDashoffset = this.totalLength;
            this.progressPath.getBoundingClientRect();
            this.progressPath.style.transition = "stroke-dashoffset 10ms linear";

            this.updateProgress();
        };

        setupEventListeners = () => {
            window.addEventListener("scroll", this.handleScroll, { passive: true });
            this.element.addEventListener("click", this.handleScrollToTop);
        };

        updateProgress = () => {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const windowHeight =
                window.innerHeight || document.documentElement.clientHeight;
            const documentHeight = Math.max(
                document.body.scrollHeight,
                document.documentElement.scrollHeight,
                document.body.offsetHeight,
                document.documentElement.offsetHeight,
                document.body.clientHeight,
                document.documentElement.clientHeight,
            );
            const offset =
                this.totalLength -
                (scrollTop * this.totalLength) / (documentHeight - windowHeight);
            this.progressPath.style.strokeDashoffset = offset.toString();
            this.updateProgressWrap(scrollTop);
        };

        updateProgressWrap = (scrollTop) => {
            this.element.classList.toggle("is-shown", scrollTop > 50);
        };

        handleScroll = () => {
            this.updateProgress();
        };

        handleScrollToTop = (event) => {
            event.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: "smooth",
            });
            return false;
        };
    }

    const totopBtn = document.querySelector(".progress-wrap");
    if (totopBtn) {
        new ProgressToTop(totopBtn);
    }
})();
