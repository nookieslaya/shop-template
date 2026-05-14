function updateButtons(track, prevButton, nextButton) {
  const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
  prevButton.disabled = track.scrollLeft <= 2;
  nextButton.disabled = track.scrollLeft >= maxScroll - 2;
}

function getCardOffsets(track) {
  const cards = Array.from(track.querySelectorAll('.product-categories__item'));
  return cards.map((card) => card.offsetLeft);
}

export function initProductCategoriesSlider() {
  const sections = document.querySelectorAll('.product-categories[data-slider="true"]');

  sections.forEach((section) => {
    const viewport = section.querySelector('.product-categories__viewport');
    const track = section.querySelector('.product-categories__track');
    const prevButton = section.querySelector('.product-categories__button--prev');
    const nextButton = section.querySelector('.product-categories__button--next');

    if (!viewport || !track || !prevButton || !nextButton) {
      return;
    }

    const scrollByCard = (direction) => {
      const offsets = getCardOffsets(track);
      if (!offsets.length) {
        return;
      }

      const current = track.scrollLeft;
      let target = current;

      if (direction > 0) {
        const next = offsets.find((offset) => offset > current + 4);
        target = typeof next === 'number' ? next : offsets[offsets.length - 1];
      } else {
        const prevCandidates = offsets.filter((offset) => offset < current - 4);
        target = prevCandidates.length ? prevCandidates[prevCandidates.length - 1] : 0;
      }

      track.scrollTo({
        left: target,
        behavior: 'smooth',
      });
    };

    prevButton.addEventListener('click', () => scrollByCard(-1));
    nextButton.addEventListener('click', () => scrollByCard(1));

    viewport.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowLeft') {
        event.preventDefault();
        scrollByCard(-1);
      }

      if (event.key === 'ArrowRight') {
        event.preventDefault();
        scrollByCard(1);
      }
    });

    track.addEventListener('scroll', () => updateButtons(track, prevButton, nextButton), { passive: true });
    window.addEventListener('resize', () => updateButtons(track, prevButton, nextButton));

    updateButtons(track, prevButton, nextButton);
  });
}
