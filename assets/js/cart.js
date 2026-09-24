(() => {
  'use strict';

  const drawer = document.querySelector('[data-tst-cart-drawer]');
  const drawerContent = drawer?.querySelector('[data-tst-cart-content]');
  let previousFocus = null;

  function showError(message) {
    window.alert(message || 'Không thể cập nhật giỏ hàng.');
  }

  function updateCartCount(count) {
    document.querySelectorAll('.tst-cart-count').forEach((element) => {
      element.textContent = String(count);
    });
  }

  function updateDrawer(html) {
    if (!drawerContent || typeof html !== 'string') {
      return;
    }

    drawerContent.innerHTML = html;
  }

  function openDrawer() {
    if (!drawer) {
      return;
    }

    if (!drawer.classList.contains('is-open')) {
      previousFocus = document.activeElement;
    }

    drawer.inert = false;
    drawer.setAttribute('aria-hidden', 'false');
    drawer.classList.add('is-open');
    document.body.classList.add('tst-cart-drawer-open');
    drawer.querySelector('.tst-cart-drawer__close')?.focus();
  }

  function closeDrawer() {
    if (!drawer?.classList.contains('is-open')) {
      return;
    }

    drawer.classList.remove('is-open');
    document.body.classList.remove('tst-cart-drawer-open');

    const returnFocus = previousFocus?.isConnected
      ? previousFocus
      : document.querySelector('[data-tst-cart-open]');

    returnFocus?.focus();

    drawer.setAttribute('aria-hidden', 'true');
    drawer.inert = true;
  }

  async function cartRequest(action, fields = {}) {
    const body = new URLSearchParams({
      action,
      nonce: window.tstData.nonce,
      ...fields,
    });
    const response = await fetch(window.tstData.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body,
    });
    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.data?.message || 'Không thể cập nhật giỏ hàng.');
    }

    return result.data;
  }

  async function refreshDrawer() {
    if (!drawerContent || !window.tstData) {
      return;
    }

    drawerContent.setAttribute('aria-busy', 'true');

    try {
      const data = await cartRequest('tst_cart_drawer_refresh');
      updateCartCount(data.count);
      updateDrawer(data.html);
    } catch (error) {
      showError(error.message);
    } finally {
      drawerContent.setAttribute('aria-busy', 'false');
    }
  }

  async function removeCartItem(button) {
    if (!drawerContent || !window.tstData) {
      return;
    }

    button.disabled = true;
    drawerContent.setAttribute('aria-busy', 'true');

    try {
      const data = await cartRequest('tst_cart_drawer_remove', {
        cart_item_key: button.dataset.tstCartRemove,
      });
      updateCartCount(data.count);
      updateDrawer(data.html);
    } catch (error) {
      showError(error.message);
      button.disabled = false;
    } finally {
      drawerContent.setAttribute('aria-busy', 'false');
    }
  }

  async function addToCart(button, customFields = null) {
    button.disabled = true;
    button.classList.add('is-loading');

    const fields = customFields || {
      product_id: button.dataset.productId,
      quantity: '1',
    };

    if (!customFields && button.dataset.variationId) {
      fields.variation_id = button.dataset.variationId;
    }

    try {
      const data = await cartRequest('tst_add_to_cart', fields);
      updateCartCount(data.count);

      if (drawer) {
        updateDrawer(data.drawer);
        openDrawer();
      }
    } catch (error) {
      showError(error.message);
    } finally {
      button.disabled = false;
      button.classList.remove('is-loading');
    }
  }

  function trapDrawerFocus(event) {
    if (event.key !== 'Tab' || !drawer?.classList.contains('is-open')) {
      return;
    }

    const focusable = [...drawer.querySelectorAll('a, button, input, select, textarea')]
      .filter((element) => !element.disabled && element.tabIndex >= 0 && element.offsetParent !== null);
    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (!first || !last) {
      return;
    }

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)) {
      return;
    }

    const addButton = event.target.closest('.tst-ajax-add');

    if (addButton && window.tstData) {
      event.preventDefault();
      addToCart(addButton);
      return;
    }

    const openButton = event.target.closest('[data-tst-cart-open]');

    if (openButton && drawer) {
      event.preventDefault();
      openDrawer();
      refreshDrawer();
      return;
    }

    if (event.target.closest('[data-tst-cart-close]')) {
      closeDrawer();
      return;
    }

    const removeButton = event.target.closest('[data-tst-cart-remove]');

    if (removeButton) {
      removeCartItem(removeButton);
    }
  });

  document.addEventListener('submit', (event) => {
    const form = event.target;

    if (
      !(form instanceof HTMLFormElement) ||
      !form.matches('.tst-product-info__purchase form.cart') ||
      !window.tstData ||
      !drawer ||
      event.submitter?.name === 'tst_buy_now'
    ) {
      return;
    }

    const button = form.querySelector('.single_add_to_cart_button');
    const formData = new FormData(form);
    const productId = formData.get('product_id') || formData.get('add-to-cart');
    const variationId = formData.get('variation_id');

    if (
      !button ||
      !productId ||
      (form.classList.contains('variations_form') && !Number(variationId))
    ) {
      return;
    }

    if (button.disabled || button.classList.contains('is-loading')) {
      event.preventDefault();
      return;
    }

    event.preventDefault();
    addToCart(button, {
      product_id: String(productId),
      quantity: String(formData.get('quantity') || '1'),
      variation_id: String(variationId || '0'),
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeDrawer();
    }

    trapDrawerFocus(event);
  });

  if (drawer && window.jQuery) {
    window.jQuery(document.body).on('added_to_cart', () => {
      openDrawer();
      refreshDrawer();
    });
  }
})();
