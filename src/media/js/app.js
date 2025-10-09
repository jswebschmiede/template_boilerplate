import './vendor/preloader';
import './vendor/joomla';
import './vendor/progress-to-top';
import './vendor/reveal-effects';
import './vendor/anim-menu-btn';
import './vendor/flexi-header';
import GdprClassObserver from './vendor/gdpr-class-observer';
import StickyHeader from './vendor/sticky-header';

import '../css/app.css';

// init
(() => {
    /**
     * Sticky Header
     */
    const stickyHeader = document.querySelector('.f-header--sticky');
    if (stickyHeader) {
        new StickyHeader(stickyHeader);
    }

    /**
     * GDPR Class Observer
     */
    new GdprClassObserver();

    /**
     * Make tables responsive by wrapping them in a responsive container
     */
    function makeTablesResponsive() {
        const entryContent = document.querySelector('[data-element="content"]');

        if (!entryContent) {
            return;
        }

        const tables = entryContent.querySelectorAll('table');
        tables.forEach(table => {
            if (table.parentElement.classList.contains('table-responsive')) {
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';

            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        });
    }

    window.addEventListener('load', makeTablesResponsive);

    /**
     * Scroll to finder form
     */
    const finderForm = document.querySelector('.com-finder');
    if (finderForm) {
        finderForm.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
            inline: 'nearest',
        });
    }

    console.log('app.js loaded');
})();
