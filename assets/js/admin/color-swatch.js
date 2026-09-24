(() => {
  'use strict';

  if (!window.jQuery?.fn?.wpColorPicker) {
    return;
  }

  document.querySelectorAll('.tst-color-swatch-field').forEach((field) => {
    window.jQuery(field).wpColorPicker();
  });
})();
