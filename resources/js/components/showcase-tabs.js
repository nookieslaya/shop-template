export function initShowcaseTabs() {
  const sections = document.querySelectorAll('.showcase-tabs');

  sections.forEach((section) => {
    const tabs = section.querySelectorAll('.showcase-tabs__tab');
    const panels = section.querySelectorAll('.showcase-tabs__panel');

    if (!tabs.length || !panels.length) {
      return;
    }

    const activateTab = (tabKey) => {
      tabs.forEach((tab) => {
        const active = tab.dataset.tab === tabKey;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-selected', String(active));
      });

      panels.forEach((panel) => {
        const visible = panel.getAttribute('data-showcase-panel') === tabKey;
        panel.classList.toggle('is-active', visible);
        panel.hidden = !visible;
      });
    };

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const tabKey = tab.dataset.tab || 'new';
        activateTab(tabKey);
      });
    });

    activateTab('new');
  });
}
