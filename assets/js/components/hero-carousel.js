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
    let mouseStartX = null;
    let mouseDragged = false;

    if (slides.length < 2 || slides.length !== dots.length) {
      return;
    }

    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        currentIndex = index;
        showSlide(slides, dots, currentIndex);
      });
    });

    hero.addEventListener('pointerdown', (event) => {
      if (event.pointerType !== 'mouse' || event.button !== 0) {
        return;
      }

      if (event.target.closest('a, button')) {
        return;
      }

      mouseStartX = event.clientX;
      mouseDragged = false;
      hero.setPointerCapture(event.pointerId);
    });

    hero.addEventListener('pointermove', (event) => {
      if (mouseStartX === null || Math.abs(event.clientX - mouseStartX) < 10) {
        return;
      }

      mouseDragged = true;
      hero.classList.add('is-dragging');
    });

    hero.addEventListener('pointerup', (event) => {
      if (mouseStartX === null) {
        return;
      }

      const distance = event.clientX - mouseStartX;
      mouseStartX = null;
      hero.classList.remove('is-dragging');

      if (Math.abs(distance) < 50) {
        return;
      }

      currentIndex = (currentIndex + (distance < 0 ? 1 : -1) + slides.length) % slides.length;
      showSlide(slides, dots, currentIndex);
    });

    hero.addEventListener('pointercancel', () => {
      mouseStartX = null;
      hero.classList.remove('is-dragging');
    });

    hero.addEventListener('click', (event) => {
      if (!mouseDragged) {
        return;
      }

      event.preventDefault();
      mouseDragged = false;
    }, true);

    const interval = Number.parseInt(hero.dataset.tstHeroInterval, 10);

    if (hero.dataset.tstHeroAutoplay === '1' && interval >= 2000 && interval <= 20000) {
      window.setInterval(() => {
        if (document.hidden || hero.matches(':hover, :focus-within')) {
          return;
        }

        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(slides, dots, currentIndex);
      }, interval);
    }

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
