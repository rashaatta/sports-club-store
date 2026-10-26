import '@fontsource-variable/cairo';
import '@fontsource/anton';

import Alpine from 'alpinejs';

import productGallery from './components/product-gallery';
import productModel from './components/product-model';
import catalogFilters from './components/catalog-filters';
import headerOverlay from './components/header-overlay';
import orderForm from './components/order-form';
import variantEditor from './components/variant-editor';
import mediaPreview from './components/media-preview';
import confirmAction from './components/confirm-action';
import copyLink from './components/copy-link';

window.Alpine = Alpine;

Alpine.data('productGallery', productGallery);
Alpine.data('productModel', productModel);
Alpine.data('catalogFilters', catalogFilters);
Alpine.data('headerOverlay', headerOverlay);
Alpine.data('orderForm', orderForm);
Alpine.data('variantEditor', variantEditor);
Alpine.data('mediaPreview', mediaPreview);
Alpine.data('confirmAction', confirmAction);
Alpine.data('copyLink', copyLink);

Alpine.store('ui', {
    menuOpen: false,
    searchOpen: false,
    closeAll() {
        this.menuOpen = false;
        this.searchOpen = false;
    },
});

Alpine.start();

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (!reducedMotion && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
    );

    const observeReveals = () => {
        document.querySelectorAll('[data-reveal]:not(.is-visible)').forEach((el) => observer.observe(el));
    };

    observeReveals();
    document.addEventListener('DOMContentLoaded', observeReveals);
} else {
    document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
}
