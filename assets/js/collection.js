(() => {
  'use strict';

  const form = document.querySelector('[data-tst-collection-filter]');

  if (!form) {
    return;
  }

  document.querySelector('.tst-collection')?.classList.add('is-enhanced');

  const minInput = form.querySelector('[data-tst-price-min]');
  const maxInput = form.querySelector('[data-tst-price-max]');
  const minRange = form.querySelector('[data-tst-price-min-range]');
  const maxRange = form.querySelector('[data-tst-price-max-range]');
  const minName = minInput?.name;
  const maxName = maxInput?.name;
  const filterPanel = document.querySelector('#tst-collection-filters');
  const filterToggle = document.querySelector('[data-tst-filter-open]');
  const mobileFilter = window.matchMedia('(max-width: 1199px)');
  let previousFocus = null;

  function closeFilters() {
    if (!filterPanel?.classList.contains('is-open')) {
      return;
    }

    filterPanel.classList.remove('is-open');
    filterPanel.removeAttribute('role');
    filterPanel.removeAttribute('aria-modal');
    filterToggle?.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('tst-collection-filter-open');
    previousFocus?.focus();
  }

  function openFilters() {
    if (!filterPanel || !filterToggle || !mobileFilter.matches) {
      return;
    }

    previousFocus = document.activeElement;
    filterPanel.classList.add('is-open');
    filterPanel.setAttribute('role', 'dialog');
    filterPanel.setAttribute('aria-modal', 'true');
    filterToggle.setAttribute('aria-expanded', 'true');
    document.body.classList.add('tst-collection-filter-open');
    filterPanel.querySelector('[data-tst-filter-close]')?.focus();
  }

  filterToggle?.addEventListener('click', openFilters);

  document.querySelectorAll('[data-tst-filter-close]').forEach((button) => {
    button.addEventListener('click', closeFilters);
  });

  document.addEventListener('keydown', (event) => {
    if (!filterPanel?.classList.contains('is-open')) {
      return;
    }

    if (event.key === 'Escape') {
      closeFilters();
      return;
    }

    if (event.key !== 'Tab') {
      return;
    }

    const focusable = Array.from(filterPanel.querySelectorAll('a, button, input, summary'))
      .filter((element) => element.getClientRects().length && !element.disabled);
    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last?.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first?.focus();
    }
  });

  mobileFilter.addEventListener('change', () => {
    if (!mobileFilter.matches) {
      closeFilters();
    }
  });

  function syncPrice(source) {
    if (!minInput || !maxInput || !minRange || !maxRange) {
      return;
    }

    const minimum = Math.max(0, Number(minInput.value) || 0);
    const maximum = Math.max(minimum, Number(maxInput.value) || 0);

    if (source === minRange) {
      minInput.value = Math.min(Number(minRange.value), maximum);
    } else if (source === maxRange) {
      maxInput.value = Math.max(Number(maxRange.value), minimum);
    } else if (minimum > Number(maxInput.value)) {
      maxInput.value = minimum;
    }

    minRange.value = minInput.value;
    maxRange.value = maxInput.value;
  }

  [minInput, maxInput, minRange, maxRange].forEach((input) => {
    input?.addEventListener('input', () => syncPrice(input));
    input?.addEventListener('change', () => {
      if (!mobileFilter.matches) {
        form.requestSubmit();
      }
    });
  });

  form.querySelectorAll('input[type="checkbox"]').forEach((input) => {
    input.addEventListener('change', () => {
      if (input.checked) {
        form.querySelectorAll(`input[name="${input.name}"]`).forEach((choice) => {
          if (choice !== input) {
            choice.checked = false;
          }
        });
      }

      if (!mobileFilter.matches) {
        form.requestSubmit();
      }
    });
  });

  form.addEventListener('submit', () => {
    if (minInput && minName) {
      minInput.name = minName;
    }

    if (maxInput && maxName) {
      maxInput.name = maxName;
    }

    if (minInput && Number(minInput.value) <= 0) {
      minInput.removeAttribute('name');
    }

    if (maxInput && maxRange && Number(maxInput.value) >= Number(maxRange.max)) {
      maxInput.removeAttribute('name');
    }
  });
})();
