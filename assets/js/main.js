(() => {
  'use strict';

  const toggle = document.querySelector('.tst-menu-toggle');
  const nav = document.querySelector('.tst-header__nav');

  if (!toggle || !nav) {
    return;
  }

  toggle.addEventListener('click', () => {
    const open = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
})();
