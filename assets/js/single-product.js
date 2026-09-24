(() => {
  'use strict';

  const product = document.querySelector('[data-tst-single-product]');

  if (!product) {
    return;
  }

  const mainImage = product.querySelector('[data-tst-gallery-main]');
  const transitionImage = product.querySelector('[data-tst-gallery-transition]');
  const stage = product.querySelector('.tst-product-gallery__stage');
  const thumbnails = [...product.querySelectorAll('[data-tst-gallery-image]')];
  const defaultImage = mainImage?.src || '';
  const colorMap = JSON.parse(product.dataset.tstColorMap || '{}');
  const price = product.querySelector('[data-tst-product-price]');
  const defaultPrice = price?.innerHTML || '';
  let slideTimer = null;
  let slideInProgress = false;
  let pendingSlide = null;

  function resetSlide(source) {
    if (!stage) {
      return;
    }

    if (source && mainImage) {
      mainImage.src = source;
    }

    stage.classList.remove('is-sliding');
    stage.offsetWidth;
  }

  function slideToImage(source, direction = 1) {
    if (!source || !mainImage) {
      return;
    }

    if (slideInProgress) {
      pendingSlide = { source, direction };
      return;
    }

    if (source === mainImage.src) {
      return;
    }

    if (!transitionImage || !stage || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      mainImage.src = source;
      return;
    }

    slideInProgress = true;
    const preload = new Image();

    preload.onload = () => {
      stage.classList.toggle('is-from-left', direction < 0);
      transitionImage.src = source;
      let finished = false;

      const finishSlide = (event) => {
        if (finished || (event && event.propertyName !== 'transform')) {
          return;
        }

        finished = true;
        window.clearTimeout(slideTimer);
        transitionImage.removeEventListener('transitionend', finishSlide);
        resetSlide(source);
        slideInProgress = false;

        const next = pendingSlide;
        pendingSlide = null;

        if (next) {
          slideToImage(next.source, next.direction);
        }
      };

      transitionImage.addEventListener('transitionend', finishSlide);
      stage.offsetWidth;
      stage.classList.add('is-sliding');
      slideTimer = window.setTimeout(finishSlide, 650);
    };

    preload.onerror = () => {
      slideInProgress = false;

      const next = pendingSlide;
      pendingSlide = null;

      if (next) {
        slideToImage(next.source, next.direction);
      }
    };

    preload.src = source;
  }

  function showImage(index, direction) {
    const thumbnail = thumbnails[index];

    if (!thumbnail || !mainImage) {
      return;
    }

    const current = thumbnails.findIndex((item) => item.classList.contains('is-active'));
    slideToImage(thumbnail.dataset.tstGalleryImage, direction || (index < current ? -1 : 1));
    thumbnails.forEach((item) => {
      const active = item === thumbnail;
      item.classList.toggle('is-active', active);
      item.setAttribute('aria-pressed', String(active));
    });
  }

  thumbnails.forEach((thumbnail, index) => {
    thumbnail.addEventListener('click', () => showImage(index));
  });

  product.querySelector('[data-tst-gallery-prev]')?.addEventListener('click', () => {
    const current = thumbnails.findIndex((item) => item.classList.contains('is-active'));
    showImage((current - 1 + thumbnails.length) % thumbnails.length, -1);
  });

  product.querySelector('[data-tst-gallery-next]')?.addEventListener('click', () => {
    const current = thumbnails.findIndex((item) => item.classList.contains('is-active'));
    showImage((current + 1) % thumbnails.length, 1);
  });

  function buildVariationChoices(select) {
    const row = select.closest('tr');
    const valueCell = select.closest('td');

    if (!row || !valueCell) {
      return;
    }

    const isColor = /(^|_)color$/.test(select.name);
    const isSize = /(^|_)size$/.test(select.name);

    if (!isColor && !isSize) {
      return;
    }

    row.classList.add(isColor ? 'tst-product-info__color-row' : 'tst-product-info__size-row');
    if (isColor) {
      const label = row.querySelector('th label');

      if (label) {
        const selected = document.createElement('span');
        selected.className = 'tst-product-info__selected-color';
        label.append(selected);
      }
    }

    const choices = document.createElement('div');
    choices.className = isColor
      ? 'tst-product-info__color-choices'
      : 'tst-product-info__size-choices';

    [...select.options].filter((option) => option.value).forEach((option) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = isColor
        ? 'tst-product-info__color-choice'
        : 'tst-product-info__size-choice';
      button.dataset.value = option.value;
      button.setAttribute('aria-label', option.textContent.trim());
      button.setAttribute('aria-pressed', 'false');

      if (isColor) {
        const swatch = document.createElement('span');
        swatch.style.backgroundColor = colorMap[option.value] || '#d9d9d9';
        button.append(swatch);
      } else {
        button.textContent = option.textContent.trim();
      }

      button.addEventListener('click', () => {
        select.value = option.value;
        select.dispatchEvent(new Event('change', { bubbles: true }));
        updateChoices(select, choices);
      });
      choices.append(button);
    });

    valueCell.append(choices);
    updateChoices(select, choices);
  }

  function updateChoices(select, choices) {
    const selectedColor = select.closest('tr')?.querySelector('.tst-product-info__selected-color');

    if (selectedColor) {
      selectedColor.textContent = select.selectedOptions[0]?.value
        ? `: ${select.selectedOptions[0].textContent.trim()}`
        : '';
    }

    choices.querySelectorAll('button').forEach((button) => {
      const option = [...select.options].find((item) => item.value === button.dataset.value);
      const active = select.value === button.dataset.value;
      button.disabled = !option || option.disabled;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-pressed', String(active));
    });
  }

  function addQuantityControls(quantity) {
    const input = quantity.querySelector('input.qty');

    if (!input) {
      return;
    }

    const row = document.createElement('div');
    const label = document.createElement('span');
    row.className = 'tst-product-info__quantity-row';
    label.textContent = 'Số lượng';
    quantity.before(row);
    row.append(label, quantity);

    const decrement = document.createElement('button');
    const increment = document.createElement('button');
    decrement.type = 'button';
    increment.type = 'button';
    decrement.textContent = '−';
    increment.textContent = '+';
    decrement.setAttribute('aria-label', 'Giảm số lượng');
    increment.setAttribute('aria-label', 'Tăng số lượng');
    quantity.prepend(decrement);
    quantity.append(increment);

    [decrement, increment].forEach((button, index) => {
      button.addEventListener('click', () => {
        const step = Number(input.step) || 1;
        const min = Number(input.min) || 1;
        const max = input.max ? Number(input.max) : Infinity;
        const direction = index === 0 ? -1 : 1;
        const next = Math.min(max, Math.max(min, (Number(input.value) || min) + direction * step));
        input.value = String(next);
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
    });
  }

  product.querySelectorAll('.variations select').forEach(buildVariationChoices);
  product.querySelectorAll('form.cart .quantity').forEach(addQuantityControls);
  product.querySelectorAll('form.cart').forEach((form) => {
    const buyNow = form.querySelector('.tst-product-info__buy-now');
    const addToCart = form.querySelector('.single_add_to_cart_button');

    if (buyNow && addToCart) {
      addToCart.before(buyNow);
    }
  });
  product.classList.add('is-enhanced');

  const accordionAnimations = new WeakMap();
  const accordionTargets = new WeakMap();

  product.querySelectorAll('.tst-product-info__accordion-item').forEach((detail) => {
    const summary = detail.querySelector('summary');

    summary?.addEventListener('click', (event) => {
      if (
        !detail.animate ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
      ) {
        return;
      }

      event.preventDefault();

      const currentTarget = accordionTargets.get(detail);
      const shouldOpen = currentTarget === undefined ? !detail.open : !currentTarget;
      const startHeight = detail.getBoundingClientRect().height;
      accordionAnimations.get(detail)?.cancel();

      if (shouldOpen) {
        detail.open = true;
      }

      const endHeight = shouldOpen ? detail.scrollHeight : summary.offsetHeight;
      detail.style.overflow = 'hidden';
      accordionTargets.set(detail, shouldOpen);

      const animation = detail.animate(
        [{ height: `${startHeight}px` }, { height: `${endHeight}px` }],
        { duration: 320, easing: 'ease-in-out' }
      );

      accordionAnimations.set(detail, animation);
      animation.onfinish = () => {
        if (accordionAnimations.get(detail) !== animation) {
          return;
        }

        detail.open = shouldOpen;
        detail.style.overflow = '';
        accordionAnimations.delete(detail);
        accordionTargets.delete(detail);
      };
    });
  });

  if (window.jQuery) {
    const variationForm = window.jQuery(product).find('.variations_form');
    const buyNow = variationForm.find('.tst-product-info__buy-now').get(0);

    if (buyNow) {
      buyNow.disabled = true;
    }

    variationForm.on('woocommerce_update_variation_values reset_data', () => {
      product.querySelectorAll('.variations select').forEach((select) => {
        const choices = select.closest('td')?.querySelector('[class$="-choices"]');

        if (choices) {
          updateChoices(select, choices);
        }
      });
    });

    variationForm.on('found_variation', (event, variation) => {
      if (buyNow) {
        buyNow.disabled = !variation?.is_purchasable || !variation?.is_in_stock;
      }

      if (price && variation?.price_html) {
        price.innerHTML = variation.price_html;
      }

      if (mainImage && variation?.image?.src) {
        slideToImage(variation.image.src);
        thumbnails.forEach((item) => {
          item.classList.remove('is-active');
          item.setAttribute('aria-pressed', 'false');
        });
      }
    });

    variationForm.on('reset_data', () => {
      if (buyNow) {
        buyNow.disabled = true;
      }

      if (price) {
        price.innerHTML = defaultPrice;
      }

      if (thumbnails.length) {
        showImage(0);
      } else {
        slideToImage(defaultImage);
      }
    });

    variationForm.on('hide_variation', () => {
      if (buyNow) {
        buyNow.disabled = true;
      }
    });
  }
})();
