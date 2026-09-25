document.addEventListener('DOMContentLoaded', () => {
  const account = document.querySelector('[data-tst-account]');

  if (!account) {
    return;
  }

  const tabs = Array.from(account.querySelectorAll('[data-tst-account-tab]'));
  const panels = Array.from(account.querySelectorAll('[data-tst-account-panel]'));

  const activateTab = (name) => {
    tabs.forEach((tab) => {
      const selected = tab.dataset.tstAccountTab === name;
      tab.setAttribute('aria-selected', String(selected));
      tab.tabIndex = selected ? 0 : -1;
    });

    panels.forEach((panel) => {
      panel.hidden = panel.dataset.tstAccountPanel !== name;
    });
  };

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      activateTab(tab.dataset.tstAccountTab);
    });
  });

  if (window.location.hash === '#tst-register') {
    activateTab('register');
  }
});
