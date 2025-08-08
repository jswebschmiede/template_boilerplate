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
    const stickyHeader = document.querySelector('.f-header--sticky');
    if (stickyHeader) {
        new StickyHeader(stickyHeader);
    }

    new GdprClassObserver();

    console.log('app.js loaded');
})();
