window.CorsecIncomingValidation = window.CorsecIncomingValidation || (() => {
    const defaultUploadOptions = {
        maxBytes: 3 * 1024 * 1024,
        label: '3 MB',
    };

    function clearValidation($form) {
        $form.find('.validation-error').remove();
        $form.find('.border-danger').removeClass('border-danger bg-danger-light');
    }

    function appendError($target, message) {
        if (!$target || $target.length === 0) return;
        if (!$target.hasClass('border-danger')) {
            $target.addClass('border-danger bg-danger-light');
        }
        $target.after(
            `<em class="mt-1 text-sm alert text-danger validation-error">${message}</em>`
        );
    }

    function showFieldError($form, fieldName, message) {
        const normalized = fieldName.replace(/\.\d+$/, '[]');
        let $field = $form.find(`[name="${fieldName}"]`);
        if ($field.length === 0) {
            $field = $form.find(`[name="${normalized}"]`);
        }
        if ($field.length > 1) {
            const $visible = $field.filter(':visible');
            if ($visible.length > 0) {
                $field = $visible.first();
            } else {
                $field = $field.first();
            }
        }
        if ($field.length === 0 && (fieldName.startsWith('files') || fieldName.startsWith('evidence_files'))) {
            $field = $form.find('[name="files[]"], [name="evidence_files[]"]');
        }
        if ($field.length === 0) {
            $field = $form.find(`[data-field="${fieldName}"]`);
        }
        if ($field.length === 0) {
            $field = $form.find(`[data-field="${normalized.replace('[]', '')}"]`);
        }
        appendError($field, message);
    }

    function uploadOptions(options = {}) {
        const maxBytes = Number(options.maxBytes || defaultUploadOptions.maxBytes);
        const label = options.label || defaultUploadOptions.label;

        return {
            maxBytes,
            label,
        };
    }

    function validateFileSizes($form, options = {}) {
        const config = uploadOptions(options);
        const errors = {};

        if (!config.maxBytes || config.maxBytes <= 0) {
            return errors;
        }

        $form.find('input[type="file"]').each(function() {
            if (!this.files || this.files.length === 0) {
                return;
            }

            const oversizedFile = Array.from(this.files).find((file) => file.size > config.maxBytes);
            if (!oversizedFile) {
                return;
            }

            const fieldName = this.name || this.id;
            if (!fieldName) {
                return;
            }

            errors[fieldName] = `Ukuran file "${oversizedFile.name}" melebihi maksimal ${config.label}.`;
        });

        return errors;
    }

    function uploadFailureMessage(xhr, fallback, options = {}) {
        const config = uploadOptions(options);

        if (xhr && (xhr.status === 413 || xhr.status === 0)) {
            return `Upload gagal. Ukuran file maksimal ${config.label} per file.`;
        }

        if (typeof window.corsecAjaxMessage === 'function') {
            return window.corsecAjaxMessage(xhr, fallback);
        }

        return fallback;
    }

    return {
        clearValidation,
        showFieldError,
        validateFileSizes,
        uploadFailureMessage,
    };
})();
