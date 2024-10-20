class StickyHeader {
  /**
   * Constructor function to initialize the StickyHeader component.
   *
   * @param {HTMLElement} element - The header element
   * @param {Object} options - The options for the StickyHeader component
   * @param {number} options.scrollThreshold - The scroll threshold for the sticky header
   * @param {boolean} options.isScrollTo - The flag to enable scroll to functionality
   * @param {number} options.scrollMarginTop - The scroll margin top for each section
   * @return {void}
   *
   */
  constructor(
    element,
    options = {
      scrollThreshold: 400,
      isScrollTo: false,
      scrollMarginTop: 112,
    },
  ) {
    this.element = element;
    this.options = { ...StickyHeader.defaultOptions, ...options };
    this.scrollThreshold = this.options.scrollThreshold;
    this.headerHeight = this.element.offsetHeight;
    this.isSticky = false;
    this.scrollTimeout = null;

    this.handleScroll = this.handleScroll.bind(this);
    this.init();
    this.setScrollMargin();
    this.initEventListeners();
    this.handleScroll();
  }

  static defaultOptions = {
    scrollThreshold: 400,
    isScrollTo: false,
    scrollMarginTop: 112,
  };

  init() {
    this.setHeaderHeight();
  }

  initEventListeners() {
    window.addEventListener("scroll", this.handleScroll);
    window.addEventListener("resize", () => {
      this.calculateHeaderHeight();
      this.setHeaderHeight();
    });
  }

  /**
   * Function to initialize the event listeners.
   *
   * @return {void}
   */
  handleScroll() {
    if (window.scrollY > this.scrollThreshold && !this.isSticky) {
      this.element.classList.add("is-sticky");
      this.isSticky = true;
    } else if (window.scrollY <= this.scrollThreshold && this.isSticky) {
      this.element.classList.remove("is-sticky");
      this.isSticky = false;
    }

    if (this.options.isScrollTo) {
      this.handleScrollTo();
    }
  }

  /**
   * Function to handle the scroll event and toggle the active class on the navigation links.
   *
   * @param {Event} event - The scroll event
   * @return {void}
   */
  handleScrollTo() {
    clearTimeout(this.scrollTimeout);

    this.scrollTimeout = setTimeout(() => {
      const sections = Array.from(document.querySelectorAll("section[id]"));
      const navLinks = Array.from(document.querySelectorAll(".header__link"));
      const currentSectionId = this.getCurrentSectionId(sections);

      navLinks.forEach((link) => {
        link.classList.toggle(
          "active",
          link.getAttribute("href") === "#" + currentSectionId,
        );
      });
    }, 50); // delay
  }

  /**
   * Function to get the id of the current section based on the window scroll position.
   *
   * @param {Array} sections - The array of section elements
   * @return {string|null} The id of the current section or null if no section is found
   */
  getCurrentSectionId(sections) {
    for (let i = sections.length - 1; i >= 0; i--) {
      const section = sections[i];
      const sectionTop = section.offsetTop;
      const sectionHeight = section.offsetHeight;
      if (window.scrollY >= sectionTop - sectionHeight / 3) {
        return section.id;
      }
    }

    return null;
  }

  /**
   * Function to set the scroll margin top for each section.
   *
   * @return {void}
   */
  setScrollMargin() {
    const sectionIds = Array.from(document.querySelectorAll("section[id]")).map(
      (section) => section.id,
    );

    sectionIds.forEach((id) => {
      const section = document.getElementById(id);
      section.style.scrollMarginTop = `${this.options.scrollMarginTop}px`;
    });
  }

  calculateHeaderHeight() {
    this.headerHeight = this.element.offsetHeight;
  }

  setHeaderHeight() {
    const body = document.querySelector("body");
    body.style.paddingTop = `${this.headerHeight}px`;
  }
}

export default StickyHeader;
