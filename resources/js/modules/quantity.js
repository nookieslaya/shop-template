export function initQuantityButtons() {
  document.querySelectorAll('.quantity').forEach((quantity) => {
    if (quantity.querySelector('.qty-btn')) {
      return;
    }

    const input = quantity.querySelector('input.qty');
    if (!input) {
      return;
    }

    const minus = document.createElement('button');
    minus.type = 'button';
    minus.className = 'qty-btn qty-btn--minus';
    minus.textContent = '-';

    const plus = document.createElement('button');
    plus.type = 'button';
    plus.className = 'qty-btn qty-btn--plus';
    plus.textContent = '+';

    quantity.prepend(minus);
    quantity.append(plus);

    minus.addEventListener('click', () => {
      const min = Number(input.min || 1);
      const next = Math.max(min, Number(input.value || 1) - 1);
      input.value = String(next);
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });

    plus.addEventListener('click', () => {
      const max = input.max ? Number(input.max) : Infinity;
      const next = Math.min(max, Number(input.value || 1) + 1);
      input.value = String(next);
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });
  });
}
