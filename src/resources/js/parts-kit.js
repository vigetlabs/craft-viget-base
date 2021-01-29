const keyCodes = {
    tab: 9,
    enter: 13,
    esc: 27,
    arrowLeft: 37,
    arrowUp: 38,
    arrowRight: 39,
    arrowDown: 40,
    f: 70,
};

// Parts kit class for expanding/collapsing sidebar
class PartsKit {
    constructor(el) {
        this.el = el;
        this.setVars();
        this.bindEvents();
        this.initSidebar();
    }

    setVars() {
        this.sidebar = this.el.querySelector("[data-sidebar]");
        this.lsSidebar = "pk-sidebar-hidden";
        this.fullscreenExpandBtn = this.el.querySelector(
            "[data-fullscreen-expand]"
        );
        this.fullscreenCloseBtn = this.el.querySelector(
            "[data-fullscreen-close]"
        );
        /** @type {HTMLInputElement} */
        this.sidebarSearch = this.el.querySelector("[data-sidebar-search]");
        /** @type {NodeListOf<HTMLElement>} */
        this.sectionItems = this.el.querySelectorAll("[data-section-item]");
        this.searchList = [...this.sectionItems].map(el => {
            return {
                el,
                searchText: el.getAttribute('data-search-text')?.toLowerCase(),
                toggle: el.closest("[data-parts-kit-toggle]")
            }
        });
    }

    bindEvents() {
        document.addEventListener("keydown", this.handleKeydown);
        this.fullscreenExpandBtn.addEventListener("click", this.toggleSidebar);
        this.fullscreenCloseBtn.addEventListener("click", this.toggleSidebar);
        this.sidebarSearch.addEventListener("input", this.handleSearchInput);
    }

    handleKeydown = ({ keyCode }) => {
        if (keyCode === keyCodes.f || keyCode === keyCodes.esc) {
            // TODO don't toggle sidebar if search input is in focus.
            this.toggleSidebar();
        }
    };

    handleSearchInput = (e) => {
        this.doSearch(this.sidebarSearch.value);
    }

    clearSearch() {
        this.sidebar.classList.remove("search-active");
        this.searchList.forEach(item => {
            item.el.classList.remove("in-search");
            item.toggle.classList.remove("in-search");
        });
    }

    /**
     *
     * @param {String} searchText
     */
    doSearch(searchText) {
        const formattedText = searchText.trim().toLowerCase();

        this.clearSearch()

        if(!formattedText) {
            return;
        }

        this.sidebar.classList.add("search-active");

        const searchResults = this.searchList
            .filter(x => x.searchText.indexOf(searchText) > -1);

        searchResults.forEach(item => {
            item.el.classList.add("in-search");
            item.toggle.classList.add("in-search");
        })
    }

    initSidebar() {
        const isHidden = localStorage.getItem(this.lsSidebar) === "true";
        this.sidebar.classList.toggle("hidden", isHidden);
        this.fullscreenExpandBtn.classList.toggle("hidden", isHidden);
        this.fullscreenCloseBtn.classList.toggle("hidden", !isHidden);
    }

    toggleSidebar = () => {
        const isHidden = this.sidebar.classList.toggle("hidden");
        this.fullscreenExpandBtn.classList.toggle("hidden");
        this.fullscreenCloseBtn.classList.toggle("hidden");

        localStorage.setItem(this.lsSidebar, isHidden);
    };
}

// Init the parts kit class
const partsKitEl = document.querySelector("[data-parts-kit]");
new PartsKit(partsKitEl);

// Slide class for animating hiding/showing
class Slide {
    constructor(el, collapsedClass = "collapsed") {
        this.el = el;
        this.collapsedClass = collapsedClass;
        this.resetHeight = this.resetHeight.bind(this);
    }

    toggle() {
        this.change("toggle");
    }

    open() {
        this.change("remove");
    }

    close() {
        this.change("add");
    }

    change(method) {
        this.el.addEventListener("transitionend", this.resetHeight, false);
        this.el.style.maxHeight = this.el.scrollHeight + "px";
        this.el.style.overflow = "hidden";

        // Wait a tick so height has applied
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                this.el.classList[method](this.collapsedClass);
            });
        });
    }

    resetHeight(e) {
        if (e.target === this.el) {
            this.el.style.overflow = "";
            this.el.style.maxHeight = "none";
            this.el.removeEventListener(
                "transitionend",
                this.resetHeight,
                false
            );
        }
    }
}

// Toggle class for sidebar navigation
class Toggle {
    constructor(el) {
        this.el = el;
        this.config = {
            onOpen: null,
            onClose: null,
            onToggle: null,
        };

        this.setVars();
        this.bindEvents();
    }

    setVars() {
        this.togglers = this.el.querySelectorAll("[aria-controls]");
        this.toggleeEl = this.el.querySelector("[data-togglee]") || this.el;
        this.toggleeClass = this.toggleeEl.getAttribute("data-togglee");
        this.togglee = this.toggleeClass
            ? new Slide(this.toggleeEl, this.toggleeClass)
            : new Slide(this.toggleeEl);

        this.isOpen =
            this.togglers.length &&
            this.togglers[0].getAttribute("aria-expanded") === "true";
    }

    bindEvents() {
        this.togglers.forEach((el) =>
            el.addEventListener("click", this.handleToggleClick)
        );
    }

    handleToggleClick = () => {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }

        this.callback("onToggle");
    };

    close = () => {
        this.isOpen = false;
        this.togglee.close();
        this.togglers.forEach((el) =>
            el.setAttribute("aria-expanded", "false")
        );
        this.el.removeAttribute("open");

        this.callback("onClose");
    };

    open = () => {
        this.isOpen = true;
        this.togglee.open();
        this.togglers.forEach((el) => el.setAttribute("aria-expanded", "true"));
        this.el.setAttribute("open", "true");

        this.callback("onOpen");
    };

    callback(prop) {
        typeof this.config[prop] === "function" && this.config[prop]();
    }

    handleKeydown = ({ keyCode }) => {
        if (this.isOpen && keyCode === keyCodes.esc) {
            this.close();
        }
    };
}

// Init the toggle class
const toggles = document.querySelectorAll("[data-parts-kit-toggle]");
toggles.forEach((el) => new Toggle(el));
