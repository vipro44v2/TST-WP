(() => {
  'use strict';

  function updateArrows(track, previous, next) {
    previous.disabled = track.scrollLeft < 5;
    next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 5;
  }

  function initPanel(panel) {
    const track = panel.querySelector('.tst-product-tabs__track');
    const previous = panel.querySelector('.tst-product-tabs__arrow--prev');
    const next = panel.querySelector('.tst-product-tabs__arrow--next');

    if (!track || !previous || !next) {
      return;
    }

    previous.addEventListener('click', () => {
      track.scrollBy({ left: -track.clientWidth, behavior: 'smooth' });
    });

    next.addEventListener('click', () => {
      track.scrollBy({ left: track.clientWidth, behavior: 'smooth' });
    });

    track.addEventListener('scroll', () => updateArrows(track, previous, next));
    updateArrows(track, previous, next);
  }

  function refreshPanelArrows(panel) {
    const track = panel.querySelector('.tst-product-tabs__track');
    const previous = panel.querySelector('.tst-product-tabs__arrow--prev');
    const next = panel.querySelector('.tst-product-tabs__arrow--next');

    if (!track || !previous || !next) {
      return;
    }

    updateArrows(track, previous, next);
  }

  function activateTab(component, tab, tabs, focus = false) {
    const panel = component.querySelector(`#${CSS.escape(tab.getAttribute('aria-controls'))}`);

    if (!panel) {
      return;
    }

    tabs.forEach((item) => {
      const active = item === tab;
      item.classList.toggle('is-active', active);
      item.setAttribute('aria-selected', String(active));
      item.tabIndex = active ? 0 : -1;
    });

    component.querySelectorAll('[data-tst-panel]').forEach((item) => {
      item.hidden = item !== panel;
    });

    const moreLink = component.querySelector('.tst-product-tabs__more');

    if (moreLink && tab.dataset.tstMoreUrl) {
      moreLink.href = tab.dataset.tstMoreUrl;
    }

    panel.querySelector('.tst-product-tabs__track')?.scrollTo({ left: 0 });
    window.requestAnimationFrame(() => refreshPanelArrows(panel));

    if (focus) {
      tab.focus();
    }
  }

  document.querySelectorAll('[data-tst-product-tabs]').forEach((component) => {
    const tabs = [...component.querySelectorAll('[data-tst-tab]')];

    if (!tabs.length) {
      return;
    }

    component.querySelectorAll('[data-tst-panel]').forEach(initPanel);

    tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => activateTab(component, tab, tabs));
      tab.addEventListener('keydown', (event) => {
        const positions = {
          ArrowRight: (index + 1) % tabs.length,
          ArrowLeft: (index - 1 + tabs.length) % tabs.length,
          Home: 0,
          End: tabs.length - 1,
        };

        if (!(event.key in positions)) {
          return;
        }

        event.preventDefault();
        activateTab(component, tabs[positions[event.key]], tabs, true);
      });
    });
  });
})();
