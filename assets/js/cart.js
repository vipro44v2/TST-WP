(() => {
  'use strict';

  function showError(message) {
    window.alert(message || 'Unable to add product.');
  }

  function updateCartCount(count) {
    document.querySelectorAll('.tst-cart-count').forEach((element) => {
      element.textContent = count;
    });
  }

  async function addToCart(button) {
    button.disabled = true;
    button.classList.add('is-loading');

    const body = new URLSearchParams({
      action: 'tst_add_to_cart',
      nonce: window.tstData.nonce,
      product_id: button.dataset.productId,
      quantity: '1',
    });

    try {
      const response = await fetch(window.tstData.ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body,
      });
      const data = await response.json();

      if (data.success) {
        updateCartCount(data.data.count);
        return;
      }

      showError(data.data.message);
    } catch (error) {
      showError();
    } finally {
      button.disabled = false;
      button.classList.remove('is-loading');
    }
  }

  document.addEventListener('click', (event) => {
    const button = event.target.closest('.tst-ajax-add');

    if (!button || !window.tstData) {
      return;
    }

    event.preventDefault();
    addToCart(button);
  });
})();
