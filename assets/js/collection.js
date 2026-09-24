(() => {
  'use strict';

  const form = document.querySelector('[data-tst-collection-filter]');

  if (!form) {
    return;
  }

  const minInput = form.querySelector('[data-tst-price-min]');
  const maxInput = form.querySelector('[data-tst-price-max]');
  const minRange = form.querySelector('[data-tst-price-min-range]');
  const maxRange = form.querySelector('[data-tst-price-max-range]');

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
    input?.addEventListener('change', () => form.requestSubmit());
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

      form.requestSubmit();
    });
  });

  form.addEventListener('submit', () => {
    if (minInput && Number(minInput.value) <= 0) {
      minInput.removeAttribute('name');
    }

    if (maxInput && maxRange && Number(maxInput.value) >= Number(maxRange.max)) {
      maxInput.removeAttribute('name');
    }
  });
})();
