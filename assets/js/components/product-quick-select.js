(() => {
  'use strict';

  function showColor(card, selected) {
    const color = selected.dataset.tstColor;

    card.querySelectorAll('[data-tst-color]').forEach((swatch) => {
      const active = swatch === selected;
      swatch.classList.toggle('is-active', active);
      swatch.setAttribute('aria-pressed', String(active));
    });

    let availableSizes = 0;

    card.querySelectorAll('[data-tst-size-color]').forEach((size) => {
      size.hidden = size.dataset.tstSizeColor !== color;

      if (!size.hidden) {
        availableSizes += 1;
      }
    });

    const emptyMessage = card.querySelector('[data-tst-no-size]');

    if (emptyMessage) {
      emptyMessage.hidden = availableSizes > 0;
    }
  }

  document.querySelectorAll('[data-tst-product-card]').forEach((card) => {
    card.querySelectorAll('[data-tst-color]').forEach((swatch) => {
      swatch.addEventListener('click', () => showColor(card, swatch));
    });
  });
})();
