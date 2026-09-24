(() => {
  'use strict';

  let updateTimer = null;

  function scheduleCartUpdate(input) {
    if (!input.value || !input.checkValidity()) {
      return;
    }

    window.clearTimeout(updateTimer);
    updateTimer = window.setTimeout(() => {
      const form = input.closest('.woocommerce-cart-form');
      const updateButton = form?.querySelector('[name="update_cart"]');

      if (!form?.isConnected || !updateButton) {
        return;
      }

      updateButton.disabled = false;
      updateButton.click();
    }, 500);
  }

  document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)) {
      return;
    }

    const button = event.target.closest('[data-tst-cart-qty]');

    if (!button || button.disabled) {
      return;
    }

    const input = button.parentElement?.querySelector('.tst-cart-qty__input');

    if (!input) {
      return;
    }

    const change = button.dataset.tstCartQty === 'increase' ? 1 : -1;
    const minimum = Number(input.min) || 1;
    const maximum = input.max ? Number(input.max) : Infinity;
    const quantity = Number(input.value) || minimum;

    const nextQuantity = Math.min(maximum, Math.max(minimum, quantity + change));

    if (nextQuantity === quantity) {
      return;
    }

    input.value = String(nextQuantity);
    input.dispatchEvent(new Event('input', { bubbles: true }));
  });

  document.addEventListener('input', (event) => {
    if (event.target instanceof HTMLInputElement && event.target.matches('.tst-cart-qty__input')) {
      scheduleCartUpdate(event.target);
    }
  });

  document.addEventListener('change', (event) => {
    if (event.target instanceof HTMLInputElement && event.target.matches('.tst-cart-qty__input')) {
      scheduleCartUpdate(event.target);
    }
  });
})();
