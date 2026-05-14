import '../css/main.css';
import { initMobileMenu } from './modules/mobile-menu.js';
import { initQuantityButtons } from './modules/quantity.js';

document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
  initQuantityButtons();
});
