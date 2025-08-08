/**
 * GdprClassObserver
 * @param {Object} options - user defined options
 * @param {Function} options.onChange - Callback function called when GDPR class changes
 * @param {boolean} options.syncToDocumentElement - Whether to sync the class to document element
 * @param {boolean} options.enableLogging - Whether to enable console logging
 */
class GdprClassObserver {
  constructor(
    options = {
      onChange: null,
      syncToDocumentElement: true,
      enableLogging: false,
    },
  ) {
    this.options = { ...options };
    this.observer = null;
    this.lastState = false;
    this.className = 'gdpr-prevent-scrolling';

    this.init();
  }

  /**
   * Initialize the GDPR class observer
   * @return {void}
   */
  init() {
    this.lastState = this.hasGdprClass();
    this.setupObserver();

    // Initial callback
    this.handleClassChange(this.lastState);

    // Make check function globally available
    window.hasGdprPreventScrolling = () => this.hasGdprClass();
  }

  /**
   * Checks if the body element has the gdpr-prevent-scrolling class
   * @returns {boolean} True if the class is present, false otherwise
   */
  hasGdprClass() {
    return document.body.classList.contains(this.className);
  }

  /**
   * Sets up a MutationObserver to watch for changes to the body class attribute
   * @return {void}
   */
  setupObserver() {
    this.observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          const currentState = this.hasGdprClass();
          if (currentState !== this.lastState) {
            this.lastState = currentState;
            this.handleClassChange(currentState);
          }
        }
      });
    });

    this.observer.observe(document.body, {
      attributes: true,
      attributeFilter: ['class'],
    });
  }

  /**
   * Handles GDPR class changes
   * @param {boolean} hasClass - Whether the class is present or not
   * @return {void}
   */
  handleClassChange(hasClass) {
    if (this.options.enableLogging) {
      console.log(`GDPR prevent scrolling is ${hasClass ? 'active' : 'inactive'}`);
    }

    if (this.options.syncToDocumentElement) {
      if (hasClass) {
        document.documentElement.classList.add(this.className);
      } else {
        document.documentElement.classList.remove(this.className);
      }
    }

    // Call custom callback if provided
    if (typeof this.options.onChange === 'function') {
      this.options.onChange(hasClass);
    }
  }

  /**
   * Manually trigger a class check
   * @return {boolean} Current state of the GDPR class
   */
  checkClass() {
    return this.hasGdprClass();
  }

  /**
   * Destroy the observer and clean up
   * @return {void}
   */
  destroy() {
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;
    }

    // Remove global function
    if (window.hasGdprPreventScrolling) {
      delete window.hasGdprPreventScrolling;
    }
  }
}

export default GdprClassObserver;
