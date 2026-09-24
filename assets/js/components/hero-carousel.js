(() => {
  'use strict';

  function showSlide(slides, dots, index) {
    slides.forEach((slide, slideIndex) => {
      const isActive = slideIndex === index;
      slide.classList.toggle('is-active', isActive);
      slide.setAttribute('aria-hidden', String(!isActive));
    });

    dots.forEach((dot, dotIndex) => {
      const isActive = dotIndex === index;
      dot.classList.toggle('is-active', isActive);
      dot.setAttribute('aria-pressed', String(isActive));
    });
  }

  document.querySelectorAll('[data-tst-hero]').forEach((hero) => {
    const slides = [...hero.querySelectorAll('[data-tst-hero-slide]')];
    const dots = [...hero.querySelectorAll('[data-tst-hero-dot]')];
    let currentIndex = 0;
    let touchStartX = null;

    if (slides.length < 2 || slides.length !== dots.length) {
      return;
    }

    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        currentIndex = index;
        showSlide(slides, dots, currentIndex);
      });
    });

    hero.addEventListener('touchstart', (event) => {
      touchStartX = event.changedTouches[0]?.clientX ?? null;
    }, { passive: true });

    hero.addEventListener('touchend', (event) => {
      const touch = event.changedTouches[0];

      if (touchStartX === null || !touch) {
        return;
      }

      const distance = touch.clientX - touchStartX;
      touchStartX = null;

      if (Math.abs(distance) < 50) {
        return;
      }

      currentIndex = (currentIndex + (distance < 0 ? 1 : -1) + slides.length) % slides.length;
      showSlide(slides, dots, currentIndex);
    }, { passive: true });
  });
})();
