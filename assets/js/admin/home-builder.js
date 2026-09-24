(() => {
  'use strict';

  const form = document.querySelector('[data-tst-builder-form]');

  if (!form) {
    return;
  }

  const list = form.querySelector('[data-tst-builder-list]');
  const labels = window.tstHomeBuilder || {};
  let draggedBlock = null;

  function blocks() {
    return [...list.querySelectorAll(':scope > [data-tst-builder-block]')];
  }

  function renumber() {
    blocks().forEach((block, index) => {
      block.querySelectorAll('[name^="tst_home_blocks["]').forEach((input) => {
        input.name = input.name.replace(/^tst_home_blocks\[[^\]]+\]/, `tst_home_blocks[${index}]`);
      });

      block.querySelector('[data-tst-builder-up]').disabled = index === 0;
      block.querySelector('[data-tst-builder-down]').disabled = index === blocks().length - 1;
      const blockId = block.querySelector('[data-tst-builder-name="id"]').value;
      const fields = block.querySelector('[data-tst-builder-fields]');
      fields.id = `tst-builder-fields-${blockId}`;
      block.querySelector('[data-tst-builder-toggle]').setAttribute('aria-controls', fields.id);
    });
  }

  function setExpanded(block, expanded) {
    const fields = block.querySelector('[data-tst-builder-fields]');
    const toggle = block.querySelector('[data-tst-builder-toggle]');
    fields.hidden = !expanded;
    toggle.setAttribute('aria-expanded', String(expanded));
    toggle.textContent = expanded ? labels.collapse : labels.expand;
  }

  function openPicker(field) {
    if (!window.wp?.media) {
      return;
    }

    const frame = window.wp.media({
      title: labels.media,
      button: { text: labels.use },
      library: { type: 'image' },
      multiple: false,
    });

    frame.on('select', () => {
      const attachment = frame.state().get('selection').first()?.toJSON();

      if (!attachment) {
        return;
      }

      field.querySelector('[data-tst-builder-image-id]').value = attachment.id;
      const preview = field.querySelector('[data-tst-builder-preview]');
      preview.src = attachment.sizes?.medium?.url || attachment.url;
      preview.hidden = false;
    });

    frame.open();
  }

  list.addEventListener('click', (event) => {
    const block = event.target.closest('[data-tst-builder-block]');

    if (!block) {
      return;
    }

    if (event.target.closest('[data-tst-builder-toggle]')) {
      setExpanded(block, block.querySelector('[data-tst-builder-fields]').hidden);
      return;
    }

    if (event.target.closest('[data-tst-builder-remove]')) {
      if (window.confirm(labels.remove)) {
        block.remove();
        renumber();
      }

      return;
    }

    if (event.target.closest('[data-tst-builder-up]')) {
      block.previousElementSibling?.before(block);
      renumber();
      return;
    }

    if (event.target.closest('[data-tst-builder-down]')) {
      block.nextElementSibling?.after(block);
      renumber();
      return;
    }

    const field = event.target.closest('[data-tst-builder-image]');

    if (field && event.target.closest('[data-tst-builder-pick]')) {
      openPicker(field);
    }

    if (field && event.target.closest('[data-tst-builder-clear]')) {
      field.querySelector('[data-tst-builder-image-id]').value = '0';
      const preview = field.querySelector('[data-tst-builder-preview]');
      preview.removeAttribute('src');
      preview.hidden = true;
    }
  });

  form.querySelector('[data-tst-builder-add]').addEventListener('click', () => {
    if (blocks().length >= 30) {
      return;
    }

    const type = form.querySelector('[data-tst-builder-type]').value;
    const template = document.querySelector(`[data-tst-builder-template="${type}"]`);

    if (!template) {
      return;
    }

    const block = template.content.firstElementChild.cloneNode(true);
    block.querySelector('[data-tst-builder-name="id"]').value =
      `block-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
    list.append(block);
    renumber();
    setExpanded(block, true);
    block.scrollIntoView({ behavior: 'smooth', block: 'center' });
  });

  list.addEventListener('dragstart', (event) => {
    const block = event.target.closest('[data-tst-builder-block]');

    if (!block || event.target.closest('input, button, select')) {
      event.preventDefault();
      return;
    }

    draggedBlock = block;
    block.classList.add('is-dragging');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', 'tst-block');
  });

  list.addEventListener('dragover', (event) => {
    const target = event.target.closest('[data-tst-builder-block]');

    if (!draggedBlock || !target || target === draggedBlock) {
      return;
    }

    event.preventDefault();
    target.classList.add('is-drop-target');
  });

  list.addEventListener('dragleave', (event) => {
    event.target.closest('[data-tst-builder-block]')?.classList.remove('is-drop-target');
  });

  list.addEventListener('drop', (event) => {
    const target = event.target.closest('[data-tst-builder-block]');

    if (!draggedBlock || !target || target === draggedBlock) {
      return;
    }

    event.preventDefault();
    const midpoint = target.getBoundingClientRect().top + target.offsetHeight / 2;
    target.insertAdjacentElement(event.clientY < midpoint ? 'beforebegin' : 'afterend', draggedBlock);
    renumber();
  });

  list.addEventListener('dragend', () => {
    blocks().forEach((block) => block.classList.remove('is-dragging', 'is-drop-target'));
    draggedBlock = null;
  });

  renumber();
  blocks().forEach((block) => setExpanded(block, false));
})();
