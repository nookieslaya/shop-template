export function initMobileMenu() {
  const toggle = document.querySelector('.site-header__menu-toggle');
  const nav = document.querySelector('.site-header__nav');

  if (!toggle || !nav) {
    return;
  }

  toggle.addEventListener('click', () => {
    const isOpen = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
  });
}
