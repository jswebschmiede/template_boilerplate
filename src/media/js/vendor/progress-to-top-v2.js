class ProgressToTopV2 {
    constructor(buttonElement) {
        this.button = buttonElement || document.getElementById("scroll-top");
        if (!this.button) return null;

        this.progressIndicator = document.querySelector("#progress-indicator");
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateProgress();
    }

    setupEventListeners() {
        window.addEventListener("scroll", this.handleScroll.bind(this), { passive: true });
        this.button.addEventListener("click", this.handleScrollToTop.bind(this));
    }

    updateProgress() {
        const scrollTop = window.scrollY;

        if (scrollTop > 200) {
            this.button.classList.add("show");

            const documentHeight = document.documentElement.scrollHeight;
            const windowHeight = window.innerHeight;

            const progress = (scrollTop / (documentHeight - windowHeight)) * 214;

            if (this.progressIndicator) {
                this.progressIndicator.style.strokeDasharray = `${progress}, 400`;
            }
        } else {
            this.button.classList.remove("show");
        }
    }

    handleScroll() {
        this.updateProgress();
    }

    handleScrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    }
}

export default ProgressToTopV2;
