window.CorsecIncomingValidation = window.CorsecIncomingValidation || (() => {
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

    return {
        clearValidation,
        showFieldError,
    };
})();
