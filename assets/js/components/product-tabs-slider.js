(() => {
  'use strict';

  function updateArrowState(track, previous, next) {
    previous.disabled = track.scrollLeft < 5;
    next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 5;
  }

  function initPanel(panel) {
    const track = panel.querySelector('.tst-product-tabs__track');
    const previous = panel.querySelector('.tst-product-tabs__arrow--prev');
    const next = panel.querySelector('.tst-product-tabs__arrow--next');

    previous.addEventListener('click', () => {
      track.scrollBy({ left: -track.clientWidth, behavior: 'smooth' });
    });
    next.addEventListener('click', () => {
      track.scrollBy({ left: track.clientWidth, behavior: 'smooth' });
    });
    track.addEventListener('scroll', () => {
      updateArrowState(track, previous, next);
    });

    updateArrowState(track, previous, next);
  }

  function activateTab(component, tab, tabs, panels) {
    tabs.forEach((item) => {
      item.classList.remove('is-active');
      item.setAttribute('aria-selected', 'false');
    });
    panels.forEach((panel) => {
      panel.hidden = true;
    });

    tab.classList.add('is-active');
    tab.setAttribute('aria-selected', 'true');

    const panel = component.querySelector(
      '[data-tst-panel="' + tab.dataset.tstTab + '"]'
    );
    panel.hidden = false;
    panel.querySelector('.tst-product-tabs__track').scrollLeft = 0;
  }

  document.querySelectorAll('[data-tst-product-tabs]').forEach((component) => {
    const tabs = component.querySelectorAll('[data-tst-tab]');
    const panels = component.querySelectorAll('[data-tst-panel]');

    panels.forEach(initPanel);
    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        activateTab(component, tab, tabs, panels);
      });
    });
  });
})();
