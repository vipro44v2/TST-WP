(() => {
  'use strict';

  const selectors = [
    '.tst-hero',
    '.tst-lifestyle',
    '.tst-craft',
    '.tst-business',
    '.tst-promo-pair',
    '.tst-category-grid',
    '.tst-product-tabs',
    '.tst-newsletter',
    '.tst-contact-feedback',
    '.tst-related-products',
    '.tst-cart-assurance',
    '.tst-product-layout',
    '.tst-section',
  ];

  if (
    !('IntersectionObserver' in window) ||
    window.matchMedia('(prefers-reduced-motion: reduce)').matches
  ) {
    return;
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) {
        return;
      }

      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, {
    rootMargin: '0px 0px -40px 0px',
    threshold: 0.05,
  });

  document.querySelectorAll(selectors.join(', ')).forEach((component) => {
    component.classList.add('tst-reveal');
    observer.observe(component);
  });
})();
