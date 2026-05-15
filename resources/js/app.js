import '../css/main.css';
import { initMobileMenu } from './modules/mobile-menu.js';
import { initQuantityButtons } from './modules/quantity.js';
import { initProductCategoriesSlider } from './components/product-categories-slider.js';
import { initFeaturedProductsSlider } from './components/featured-products-slider.js';
import { initShowcaseTabs } from './components/showcase-tabs.js';

document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
  initQuantityButtons();
  initProductCategoriesSlider();
  initFeaturedProductsSlider();
  initShowcaseTabs();
});
