(() => {
    'use strict';

    const formatSize = (bytes) => {
        if (bytes < 1024 * 1024) return `${Math.max(1, Math.round(bytes / 1024))} KB`;
        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    };

    document.querySelectorAll('[data-gallery-manager]').forEach((manager) => {
        const upload = manager.querySelector('[data-gallery-upload]');
        const selection = manager.querySelector('[data-gallery-selection]');
        const status = manager.querySelector('[data-gallery-status]');
        const total = manager.querySelector('[data-gallery-total]');
        const removals = Array.from(manager.querySelectorAll('.js-gallery-remove'));
        const existingCount = Number(manager.dataset.existingCount) || 0;
        const maxImages = Number(manager.dataset.maxGallery) || 12;
        const previewUrls = [];

        if (!upload || !selection || !status || !total) return;

        const markedForRemoval = () => removals.filter((input) => input.checked).length;
        const currentImages = () => Math.max(0, existingCount - markedForRemoval());
        const availableSlots = () => Math.max(0, maxImages - currentImages());

        const revokePreviewUrls = () => {
            previewUrls.forEach((url) => URL.revokeObjectURL(url));
            previewUrls.length = 0;
        };

        const renderSelectedFiles = () => {
            revokePreviewUrls();
            selection.replaceChildren();
            const files = Array.from(upload.files || []);
            selection.hidden = files.length === 0;

            files.forEach((file, index) => {
                const card = document.createElement('div');
                card.className = 'admin-gallery-selected-item';

                const image = document.createElement('img');
                const url = URL.createObjectURL(file);
                previewUrls.push(url);
                image.src = url;
                image.alt = '';
                image.loading = 'lazy';

                const copy = document.createElement('div');
                const name = document.createElement('strong');
                name.textContent = file.name;
                const meta = document.createElement('small');
                meta.textContent = `${formatSize(file.size)} · ${index + 1}`;
                copy.append(name, meta);
                card.append(image, copy);
                selection.append(card);
            });
        };

        const refresh = () => {
            const selectedCount = (upload.files || []).length;
            const slots = availableSlots();
            const tooMany = selectedCount > slots;
            const projectedCount = currentImages() + selectedCount;

            upload.disabled = slots === 0 && selectedCount === 0;
            removals.forEach((input) => {
                const label = input.closest('label');
                const text = label && label.querySelector('span');
                if (text) {
                    text.textContent = input.checked
                        ? (manager.dataset.textWillRemove || 'Will be removed')
                        : (manager.dataset.textMarkRemove || 'Mark for removal');
                }
                if (label) label.classList.toggle('is-marked-for-removal', input.checked);
            });

            total.textContent = `${projectedCount} / ${maxImages}`;
            total.classList.toggle('is-over-limit', projectedCount > maxImages);

            if (tooMany) {
                status.textContent = manager.dataset.textTooMany || 'Too many images selected.';
                status.classList.add('is-error');
                upload.setCustomValidity(status.textContent);
            } else if (selectedCount > 0) {
                const template = manager.dataset.textSelected || ':count images selected.';
                status.textContent = template.replace(':count', String(selectedCount));
                const availability = manager.dataset.textAvailable || ':count gallery slots available.';
                status.textContent += ` ${availability.replace(':count', String(slots - selectedCount))}`;
                status.classList.remove('is-error');
                upload.setCustomValidity('');
            } else if (slots === 0) {
                status.textContent = manager.dataset.textFull || 'The gallery has reached the image limit.';
                status.classList.remove('is-error');
                upload.setCustomValidity('');
            } else {
                status.textContent = manager.dataset.textNoFiles || 'No new images selected.';
                status.classList.remove('is-error');
                upload.setCustomValidity('');
            }
        };

        upload.addEventListener('change', () => {
            renderSelectedFiles();
            refresh();
        });
        removals.forEach((input) => input.addEventListener('change', refresh));

        const form = manager.closest('form');
        if (form) {
            form.addEventListener('submit', (event) => {
                if (upload.files.length > availableSlots()) {
                    event.preventDefault();
                    refresh();
                    upload.reportValidity();
                    upload.focus();
                }
            });
        }

        refresh();
    });

    document.querySelectorAll('[data-main-image-manager]').forEach((manager) => {
        const upload = manager.querySelector('[data-main-image-upload]');
        const preview = manager.querySelector('[data-main-image-preview]');
        const status = manager.querySelector('[data-main-image-status]');
        if (!upload || !preview || !status) return;

        let previewUrl = null;

        upload.addEventListener('change', () => {
            const file = upload.files && upload.files[0];
            if (!file) {
                status.textContent = manager.dataset.textNoReplacement || 'No replacement selected.';
                return;
            }

            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = URL.createObjectURL(file);

            let image = preview.querySelector('img');
            if (!image) {
                preview.replaceChildren();
                image = document.createElement('img');
                image.alt = '';
                image.width = 360;
                image.height = 270;
                preview.append(image);
            }

            image.src = previewUrl;
            image.className = 'admin-primary-image-live-preview';
            status.textContent = (manager.dataset.textReplacementSelected || 'New image selected: :filename').replace(':filename', file.name);
        });
    });
})();
