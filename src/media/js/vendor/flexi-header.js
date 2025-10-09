(() => {
    /**
     * FlexiHeader
     * @param {HTMLElement} element
     * @param {Object} options - user defined options
     * @param {String} options.openSubmenu - "click" or "hover"
     */
    class FlexiHeader {
        constructor(
            element,
            options = {
                openSubmenu: "click",
            },
        ) {
            this.header = element;
            this.options = { ...FlexiHeader.defaultOptions, ...options };
            this.menuTrigger = this.header.querySelector(".js-anim-menu-btn");
            this.firstFocusableElement = this.getMenuFirstFocusable();
            // we'll use these to store the node that needs to receive focus when the mobile menu is closed
            this.focusMenu = false;
            this.resizingId = false;

            this.init();
        }

        static defaultOptions = {
            openSubmenu: "click",
        };

        init = () => {
            this.resetFlexHeaderOffset();
            this.setAriaButtons();

            this.menuTrigger.addEventListener("anim-menu-btn-clicked", (event) => {
                this.toggleMenuNavigation(event.detail);
            });

            // detect click on a dropdown control button - expand-on-mobile only
            this.header.addEventListener("click", (event) => {
                const btnLink = event.target.closest(".js-f-header__dropdown-control");
                if (!btnLink) return;
                !btnLink.getAttribute("aria-expanded")
                    ? btnLink.setAttribute("aria-expanded", "true")
                    : btnLink.removeAttribute("aria-expanded");
            });

            if (this.options.openSubmenu == "hover") {
                this.header.classList.add("f-header--open-submenu-hover");
                this.header.addEventListener("mouseout", (event) => {
                    const btnLink = event.target.closest(
                        ".js-f-header__dropdown-control",
                    );
                    if (!btnLink) return;
                    // check layout type
                    if (this.getLayout() == "mobile") return;
                    btnLink.removeAttribute("aria-expanded");
                });
            }

            // close dropdown on focusout - expand-on-mobile only
            this.header.addEventListener("focusin", (event) => {
                const btnLink = event.target.closest(".js-f-header__dropdown-control"),
                    dropdown = event.target.closest(".f-header__dropdown");
                if (dropdown) return;
                if (btnLink && btnLink.hasAttribute("aria-expanded")) return;
                // check layout type
                if (this.getLayout() == "mobile") return;
                const openDropdown = this.header.querySelector(
                    '.js-f-header__dropdown-control[aria-expanded="true"]',
                );
                if (openDropdown) openDropdown.removeAttribute("aria-expanded");
            });

            // listen for resize
            window.addEventListener("resize", () => {
                clearTimeout(this.resizingId);
                this.resizingId = setTimeout(this.doneResizing, 500);
            });
        };

        setAriaButtons = () => {
            var btnDropdown = this.header.getElementsByClassName(
                "js-f-header__dropdown-control",
            );

            for (var i = 0; i < btnDropdown.length; i++) {
                var id = "f-header-dropdown-" + i,
                    dropdown = btnDropdown[i].nextElementSibling;
                if (dropdown.hasAttribute("id")) {
                    id = dropdown.getAttribute("id");
                } else {
                    dropdown.setAttribute("id", id);
                }
                btnDropdown[i].setAttribute("aria-controls", id);
            }
        };

        resetFlexHeaderOffset = () => {
            // on mobile -> update max height of the flexi header based on its offset value (e.g., if there's a fixed pre-header element)
            document.documentElement.style.setProperty(
                "--f-header-offset",
                this.header.getBoundingClientRect().top + "px",
            );
        };

        toggleMenuNavigation = (bool) => {
            // toggle menu visibility on small devices
            document
                .querySelector(".f-header__nav")
                .classList.toggle("f-header__nav--is-visible", bool);
            this.header.classList.toggle("f-header--expanded", bool);
            this.menuTrigger.setAttribute("aria-expanded", bool);
            if (bool)
                this.firstFocusableElement.focus(); // move focus to first focusable element
            else if (this.focusMenu) {
                this.focusMenu.focus();
                this.focusMenu = false;
            }
        };

        isVisible = (element) => {
            return (
                element.offsetWidth ||
                element.offsetHeight ||
                element.getClientRects().length
            );
        };

        doneResizing = () => {
            if (
                !this.isVisible(this.menuTrigger) &&
                this.header.classList.contains("f-header--expanded")
            ) {
                this.menuTrigger.click();
            }
            this.resetFlexHeaderOffset();
        };

        getMenuFirstFocusable = () => {
            var focusableEle = this.header
                .querySelector(".f-header__nav")
                .querySelectorAll(
                    '[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex]:not([tabindex="-1"]), [contenteditable], audio[controls], video[controls], summary',
                ),
                firstFocusable = false;
            for (var i = 0; i < focusableEle.length; i++) {
                if (
                    focusableEle[i].offsetWidth ||
                    focusableEle[i].offsetHeight ||
                    focusableEle[i].getClientRects().length
                ) {
                    firstFocusable = focusableEle[i];
                    break;
                }
            }
            return firstFocusable;
        };

        getLayout = () => {
            return getComputedStyle(this.header, ":before")
                .getPropertyValue("content")
                .replace(/\'|"/g, "");
        };
    }

    const flexiHeader = document.querySelector(".f-header");
    if (flexiHeader) {
        new FlexiHeader(flexiHeader, { openSubmenu: "hover" });
    }
})();
