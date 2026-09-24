(() => {
  'use strict';

  const labels = window.tstHeroSettings || {};

  function setStatus(field, message, isError = false) {
    const status = field.querySelector('[data-tst-media-status]');
    status.textContent = message;
    status.classList.toggle('is-error', isError);
  }

  function setImage(field, id, url) {
    field.querySelector('[data-tst-media-id]').value = String(id);
    field.querySelector('[data-tst-media-preview]').src = url;
    setStatus(field, labels.saved);
  }

  function openMediaPicker(field) {
    if (!window.wp?.media) {
      return;
    }

    const frame = window.wp.media({
      title: labels.title,
      button: { text: labels.button },
      library: { type: 'image' },
      multiple: false,
    });

    frame.on('select', () => {
      const attachment = frame.state().get('selection').first()?.toJSON();

      if (!attachment) {
        return;
      }

      setImage(
        field,
        attachment.id,
        attachment.sizes?.medium?.url || attachment.url
      );
    });

    frame.open();
  }

  async function uploadImage(field, file) {
    if (!file.type.startsWith('image/')) {
      setStatus(field, labels.invalidFile, true);
      return;
    }

    const dropzone = field.querySelector('[data-tst-dropzone]');
    const body = new FormData();
    body.append('action', 'tst_upload_hero_image');
    body.append('nonce', labels.nonce);
    body.append('image', file, file.name);

    dropzone.classList.add('is-uploading');
    setStatus(field, labels.uploading);

    try {
      const response = await fetch(labels.ajaxUrl, {
        method: 'POST',
        body,
        credentials: 'same-origin',
      });
      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.data?.message || labels.uploadError);
      }

      setImage(field, result.data.id, result.data.url);
    } catch (error) {
      setStatus(field, error.message || labels.uploadError, true);
    } finally {
      dropzone.classList.remove('is-uploading');
    }
  }

  function isFileDrag(event) {
    return [...(event.dataTransfer?.types || [])].includes('Files');
  }

  function initMediaField(field) {
    const dropzone = field.querySelector('[data-tst-dropzone]');

    field.querySelector('[data-tst-media-select]').addEventListener('click', () => {
      openMediaPicker(field);
    });

    field.querySelector('[data-tst-media-clear]').addEventListener('click', () => {
      field.querySelector('[data-tst-media-id]').value = '0';
      field.querySelector('[data-tst-media-preview]').src = field.dataset.tstDefaultUrl;
      setStatus(field, labels.saved);
    });

    dropzone.addEventListener('dragover', (event) => {
      if (!isFileDrag(event)) {
        return;
      }

      event.preventDefault();
      dropzone.classList.add('is-drag-over');
    });

    dropzone.addEventListener('dragleave', (event) => {
      if (!dropzone.contains(event.relatedTarget)) {
        dropzone.classList.remove('is-drag-over');
      }
    });

    dropzone.addEventListener('drop', (event) => {
      dropzone.classList.remove('is-drag-over');

      if (!isFileDrag(event)) {
        return;
      }

      event.preventDefault();
      const file = event.dataTransfer.files[0];

      if (file) {
        uploadImage(field, file);
      }
    });
  }

  function getSlides(list) {
    return [...list.querySelectorAll(':scope > tbody[data-tst-slide]')];
  }

  function updateOrder(list, orderInput) {
    const slides = getSlides(list);
    orderInput.value = slides.map((slide) => slide.dataset.tstSlide).join(',');

    slides.forEach((slide, index) => {
      slide.querySelector('[data-tst-slide-up]').disabled = index === 0;
      slide.querySelector('[data-tst-slide-down]').disabled =
        index === slides.length - 1;
    });
  }

  function initSlideOrder(list, orderInput) {
    let draggedSlide = null;

    getSlides(list).forEach((slide) => {
      slide.querySelector('[data-tst-slide-up]').addEventListener('click', () => {
        const previous = slide.previousElementSibling;

        if (previous?.matches('[data-tst-slide]')) {
          list.insertBefore(slide, previous);
          updateOrder(list, orderInput);
        }
      });

      slide.querySelector('[data-tst-slide-down]').addEventListener('click', () => {
        const next = slide.nextElementSibling;

        if (next?.matches('[data-tst-slide]')) {
          list.insertBefore(next, slide);
          updateOrder(list, orderInput);
        }
      });
    });

    list.addEventListener('dragstart', (event) => {
      const handle = event.target.closest('[data-tst-slide-handle]');

      if (!handle) {
        return;
      }

      draggedSlide = handle.closest('[data-tst-slide]');
      draggedSlide.classList.add('is-dragging');
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', draggedSlide.dataset.tstSlide);
    });

    list.addEventListener('dragover', (event) => {
      const target = event.target.closest('[data-tst-slide]');

      if (!draggedSlide || !target || target === draggedSlide) {
        return;
      }

      event.preventDefault();
      event.dataTransfer.dropEffect = 'move';
      getSlides(list).forEach((slide) => {
        slide.classList.toggle('is-drop-target', slide === target);
      });
    });

    list.addEventListener('drop', (event) => {
      const target = event.target.closest('[data-tst-slide]');

      if (!draggedSlide || !target || target === draggedSlide) {
        return;
      }

      event.preventDefault();
      const midpoint = target.getBoundingClientRect().top + target.offsetHeight / 2;
      list.insertBefore(
        draggedSlide,
        event.clientY < midpoint ? target : target.nextElementSibling
      );
      updateOrder(list, orderInput);
    });

    list.addEventListener('dragend', () => {
      getSlides(list).forEach((slide) => {
        slide.classList.remove('is-dragging', 'is-drop-target');
      });
      draggedSlide = null;
    });

    updateOrder(list, orderInput);
  }

  document.querySelectorAll('[data-tst-media-field]').forEach(initMediaField);

  const slideList = document.querySelector('[data-tst-slide-list]');
  const orderInput = document.querySelector('[data-tst-slide-order]');

  if (slideList && orderInput) {
    initSlideOrder(slideList, orderInput);
  }
})();
