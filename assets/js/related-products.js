(() => {
  'use strict';

  function initSlider(slider) {
    const track = slider.querySelector('[data-tst-related-track]');
    const previous = slider.querySelector('[data-tst-related-prev]');
    const next = slider.querySelector('[data-tst-related-next]');

    if (!track || !previous || !next) {
      return;
    }

    function updateArrows() {
      previous.disabled = track.scrollLeft <= 2;
      next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
    }

    function move(direction) {
      const card = track.querySelector('.tst-product-card');

      if (!card) {
        return;
      }

      const gap = parseFloat(window.getComputedStyle(track).columnGap) || 0;
      const distance = card.getBoundingClientRect().width + gap;
      const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      track.scrollBy({
        left: direction * distance,
        behavior: reducedMotion ? 'auto' : 'smooth',
      });
    }

    previous.addEventListener('click', () => move(-1));
    next.addEventListener('click', () => move(1));
    track.addEventListener('scroll', updateArrows, { passive: true });
    window.addEventListener('resize', updateArrows);
    window.requestAnimationFrame(updateArrows);
  }

  document.querySelectorAll('[data-tst-related-products]').forEach(initSlider);
})();
