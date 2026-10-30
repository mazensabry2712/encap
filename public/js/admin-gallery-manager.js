(() => {
    'use strict';

    const formatSize = (bytes) => {
        if (bytes < 1024 * 1024) return Math.max(1, Math.round(bytes / 1024)) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    };

    // Keep earlier file selections when the browser replaces the input FileList.
    const appendSelectedFiles = (input, selectedFiles) => {
        const incoming = Array.from(input.files || []);
        if (incoming.length === 0) return;

        const identity = (file) => [
            file.name,
            file.size,
            file.lastModified,
            file.type,
            file.webkitRelativePath || '',
        ].join('|');
        const merged = [...selectedFiles];
        const seen = new Set(merged.map(identity));

        incoming.forEach((file) => {
            const key = identity(file);
            if (!seen.has(key)) {
                merged.push(file);
                seen.add(key);
            }
        });

        try {
            const transfer = new DataTransfer();
            merged.forEach((file) => transfer.items.add(file));
            input.files = transfer.files;
            selectedFiles.splice(0, selectedFiles.length, ...merged);
        } catch (error) {
            selectedFiles.splice(0, selectedFiles.length, ...incoming);
        }
    };

    document.querySelectorAll('[data-gallery-manager]').forEach((manager) => {
        const upload = manager.querySelector('[data-gallery-upload]');
        const selection = manager.querySelector('[data-gallery-selection]');
        const status = manager.querySelector('[data-gallery-status]');
        const total = manager.querySelector('[data-gallery-total]');
        const removals = Array.from(manager.querySelectorAll('.js-gallery-remove'));
        const existingCount = Number(manager.dataset.existingCount) || 0;
        const previewUrls = [];
        const selectedFiles = [];

        if (!upload || !selection || !status || !total) return;

        const markedForRemoval = () => removals.filter((input) => input.checked).length;
        const currentImages = () => Math.max(0, existingCount - markedForRemoval());

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
                meta.textContent = formatSize(file.size) + ' · ' + (index + 1);
                copy.append(name, meta);
                card.append(image, copy);
                selection.append(card);
            });
        };

        const mainImageUpload = manager.closest('form')?.querySelector('[data-main-image-upload]');
        const selectedBatchGalleryCount = () => Math.max(0, Number(mainImageUpload?.files?.length || 0) - 1);

        const refresh = () => {
            const selectedCount = (upload.files || []).length;
            const batchGalleryCount = selectedBatchGalleryCount();
            const totalSelectedGalleryCount = selectedCount + batchGalleryCount;
            const projectedCount = currentImages() + totalSelectedGalleryCount;

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

            total.textContent = String(projectedCount);

            if (totalSelectedGalleryCount > 0) {
                const template = manager.dataset.textSelected || ':count images selected.';
                status.textContent = template.replace(':count', String(totalSelectedGalleryCount));
            } else {
                status.textContent = manager.dataset.textNoFiles || 'No new images selected.';
            }

            status.classList.remove('is-error');
        };

        upload.addEventListener('change', () => {
            appendSelectedFiles(upload, selectedFiles);
            renderSelectedFiles();
            refresh();
        });
        mainImageUpload?.addEventListener('change', refresh);
        removals.forEach((input) => input.addEventListener('change', refresh));
        manager.closest('form')?.addEventListener('encap:category-gallery-change', refresh);

        refresh();
    });

    document.querySelectorAll('[data-main-image-manager]').forEach((manager) => {
        const upload = manager.querySelector('[data-main-image-upload]');
        const preview = manager.querySelector('[data-main-image-preview]');
        const selection = manager.querySelector('[data-main-image-selection]');
        const status = manager.querySelector('[data-main-image-status]');
        const form = manager.closest('form');
        const removals = Array.from(form?.querySelectorAll('.js-gallery-remove') || []);
        const initialPreviewMarkup = preview?.innerHTML || '';
        const previewUrls = [];
        const selectedFiles = [];
        let primaryPreviewUrl = null;

        if (!upload || !preview || !selection || !status) return;

        const revokePreviews = () => {
            previewUrls.forEach((url) => URL.revokeObjectURL(url));
            previewUrls.length = 0;
        };

        const renderBatchPreview = (files) => {
            revokePreviews();
            selection.replaceChildren();
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
                const role = document.createElement('strong');
                role.textContent = index === 0
                    ? (manager.dataset.textMainImageLabel || 'Main image')
                    : (manager.dataset.textGalleryImageLabel || 'Gallery image :number').replace(':number', String(index));
                const filename = document.createElement('small');
                filename.textContent = file.name;
                copy.append(role, filename);
                card.append(image, copy);
                selection.append(card);
            });
        };

        const refresh = () => {
            const files = Array.from(upload.files || []);

            if (files.length > 0) {
                let primary = preview.querySelector('img');
                if (!primary) {
                    preview.replaceChildren();
                    primary = document.createElement('img');
                    primary.alt = '';
                    primary.width = 360;
                    primary.height = 270;
                    preview.append(primary);
                }

                if (primaryPreviewUrl) URL.revokeObjectURL(primaryPreviewUrl);
                primaryPreviewUrl = URL.createObjectURL(files[0]);
                primary.src = primaryPreviewUrl;
                primary.classList.add('admin-primary-image-live-preview');
                status.textContent = (manager.dataset.textBatchSelected || ':count product photos selected.')
                    .replace(':count', String(files.length));
                if (files.length > 1) {
                    const note = (manager.dataset.textGalleryCount || ':count will be added to the gallery.')
                        .replace(':count', String(files.length - 1));
                    status.textContent += ' ' + note;
                }
            } else {
                if (primaryPreviewUrl) {
                    URL.revokeObjectURL(primaryPreviewUrl);
                    primaryPreviewUrl = null;
                }
                preview.innerHTML = initialPreviewMarkup;
                status.textContent = manager.dataset.textNoReplacement || 'No product photos selected.';
            }

            status.classList.remove('is-error');
            renderBatchPreview(files);
            form?.dispatchEvent(new Event('encap:category-gallery-change'));
        };

        upload.addEventListener('change', () => {
            appendSelectedFiles(upload, selectedFiles);
            refresh();
        });
        removals.forEach((input) => input.addEventListener('change', refresh));

        refresh();
    });
})();
