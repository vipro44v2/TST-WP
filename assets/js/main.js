(() => {
  'use strict';

  const menuToggle = document.querySelector('.tst-menu-toggle');
  const nav = document.querySelector('.tst-header__nav');
  const searchToggle = document.querySelector('[data-tst-search-toggle]');
  const searchPanel = document.querySelector('#tst-header-search');

  function closeMenu() {
    if (!menuToggle || !nav) {
      return;
    }

    nav.classList.remove('is-open');
    menuToggle.setAttribute('aria-expanded', 'false');
  }

  function closeSearch() {
    if (!searchToggle || !searchPanel) {
      return;
    }

    searchPanel.hidden = true;
    searchToggle.setAttribute('aria-expanded', 'false');
  }

  menuToggle?.addEventListener('click', () => {
    if (!nav) {
      return;
    }

    const isOpen = nav.classList.toggle('is-open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
    closeSearch();
  });

  searchToggle?.addEventListener('click', () => {
    if (!searchPanel) {
      return;
    }

    searchPanel.hidden = !searchPanel.hidden;
    searchToggle.setAttribute('aria-expanded', String(!searchPanel.hidden));
    closeMenu();

    if (!searchPanel.hidden) {
      searchPanel.querySelector('input[type="search"]')?.focus();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
      return;
    }

    closeMenu();
    closeSearch();
  });

  document.addEventListener('click', (event) => {
    if (event.target instanceof Element && !event.target.closest('.tst-header')) {
      closeMenu();
      closeSearch();
    }
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth > 1350) {
      closeMenu();
    }
  });
})();
