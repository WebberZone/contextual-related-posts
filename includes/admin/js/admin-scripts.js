// Function to clear the cache.
function crpClearCache() {
    // Get the button
    const button = document.getElementById('cache_clear');

    // Disable button and change text
    if (button) {
        button.disabled = true;
        button.textContent = crp_admin_data.strings.clearing_cache;
        button.insertAdjacentHTML('beforeend', ' <span class="spinner is-active"></span>');
    }

    // Send AJAX request
    jQuery.post(ajaxurl, {
        action: 'crp_clear_cache',
        security: crp_admin_data.security
    }, function (response, textStatus, jqXHR) {
        // Re-enable button and restore text
        if (button) {
            button.disabled = false;
            button.textContent = crp_admin_data.strings.clear_cache;
            const spinner = button.querySelector('.spinner');
            if (spinner) spinner.remove();
        }

        // Show response message
        alert(response.message);
    }, 'json').fail(function () {
        // Re-enable button and restore text on error
        if (button) {
            button.disabled = false;
            button.textContent = crp_admin_data.strings.clear_cache;
            const spinner = button.querySelector('.spinner');
            if (spinner) spinner.remove();
        }
    });

    return false;
}

/**
 * Handle thumbnail style changes to disable/enable related settings.
 */
function crpHandleThumbnailStyleChange() {
    const styleSelect = document.querySelector('select[name="crp_settings[crp_styles]"]');
    if (!styleSelect) return;

    // Configuration for disabling fields based on style
    const disableConfig = {
        'rounded_thumbs': {
            targets: ['show_excerpt', 'show_author', 'show_date', 'post_thumb_op'],
            messageKey: 'rounded_style_message'
        },
        'thumbs_grid': {
            targets: ['show_excerpt', 'show_author', 'show_date', 'post_thumb_op'],
            messageKey: 'rounded_style_message'
        },
        'text_only': {
            targets: ['post_thumb_op'],
            messageKey: 'text_only_message'
        }
    };

    function updateFields() {
        const selectedStyle = styleSelect.value;
        const config = disableConfig[selectedStyle];

        const allTargets = ['show_excerpt', 'show_author', 'show_date', 'post_thumb_op'];

        allTargets.forEach(target => {
            const isDisabled = config && config.targets.includes(target);
            const fieldType = target === 'post_thumb_op' ? 'radio' : 'checkbox';
            const selector = fieldType === 'radio' ? `input[name="crp_settings[${target}]"]` : `input[type="checkbox"][name="crp_settings[${target}]"]`;
            const elements = document.querySelectorAll(selector);

            if (elements.length > 0) {
                const container = elements[0].closest('.form-field') || elements[0].parentElement;
                const existingMessage = container.querySelector('.crp-js-message');

                if (isDisabled) {
                    elements.forEach(el => {
                        el.disabled = true;
                        if (fieldType === 'checkbox') {
                            el.checked = false;
                        }
                    });
                    if (!existingMessage) {
                        const message = document.createElement('p');
                        message.className = 'description crp-js-message';
                        message.style.color = '#9B0800';
                        message.textContent = crp_admin_data.strings[config.messageKey];
                        container.appendChild(message);
                    } else if (existingMessage.textContent !== crp_admin_data.strings[config.messageKey]) {
                        existingMessage.textContent = crp_admin_data.strings[config.messageKey];
                    }
                } else {
                    elements.forEach(el => {
                        el.disabled = false;
                    });
                    if (existingMessage) {
                        existingMessage.remove();
                    }
                }
            }
        });

        // Special handling for rounded styles: set post_thumb_op to 'inline' if not already 'inline' or 'thumbs_only'
        if (['rounded_thumbs', 'thumbs_grid'].includes(selectedStyle)) {
            const inlineRadio = document.querySelector('input[name="crp_settings[post_thumb_op]"][value="inline"]');
            const thumbsOnlyRadio = document.querySelector('input[name="crp_settings[post_thumb_op]"][value="thumbs_only"]');
            const currentChecked = document.querySelector('input[name="crp_settings[post_thumb_op]"]:checked');
            if (currentChecked && ![inlineRadio, thumbsOnlyRadio].includes(currentChecked)) {
                if (inlineRadio) {
                    inlineRadio.checked = true;
                }
            }
        }

        // Special handling for text_only style: set post_thumb_op to 'text_only'
        if (selectedStyle === 'text_only') {
            const textOnlyRadio = document.querySelector('input[name="crp_settings[post_thumb_op]"][value="text_only"]');
            if (textOnlyRadio) {
                textOnlyRadio.checked = true;
            }
        }
    }

    // Initial check
    updateFields();

    // Listen for changes
    styleSelect.addEventListener('change', updateFields);
}

/**
 * Copy text to clipboard
 * 
 * @param {string} elementId - ID of the element containing text to copy
 * @returns {void}
 */
function crpCopyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const button = element.parentElement.querySelector('.crp-copy-button');
    if (!button) return;

    navigator.clipboard.writeText(element.textContent).then(() => {
        const icon = button.querySelector('.dashicons');
        icon.classList.remove('dashicons-clipboard');
        icon.classList.add('dashicons-yes');
        button.classList.add('copied');
        button.title = crpAdmin.copied;

        setTimeout(() => {
            icon.classList.remove('dashicons-yes');
            icon.classList.add('dashicons-clipboard');
            button.classList.remove('copied');
            button.title = crpAdmin.copyToClipboard;
        }, 2000);
    }).catch(() => {
        const icon = button.querySelector('.dashicons');
        icon.classList.remove('dashicons-clipboard');
        icon.classList.add('dashicons-warning');
        button.classList.add('error');
        button.title = crpAdmin.copyError;

        setTimeout(() => {
            icon.classList.remove('dashicons-warning');
            icon.classList.add('dashicons-clipboard');
            button.classList.remove('error');
            button.title = crpAdmin.copyToClipboard;
        }, 2000);
    });
}

/**
 * Add copy button to code blocks
 * 
 * @param {string} elementId - ID of the element to add copy button to
 * @returns {void}
 */
function crpAddCopyButton(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'crp-copy-button';
    button.setAttribute('aria-label', crpAdmin.copyToClipboard);
    button.title = crpAdmin.copyToClipboard;
    button.onclick = () => crpCopyToClipboard(elementId);

    const screenReaderText = document.createElement('span');
    screenReaderText.className = 'screen-reader-text';
    screenReaderText.textContent = crpAdmin.copyToClipboard;

    const icon = document.createElement('span');
    icon.className = 'dashicons dashicons-clipboard';
    icon.setAttribute('aria-hidden', 'true');

    button.appendChild(screenReaderText);
    button.appendChild(icon);

    const wrapper = element.parentElement;
    if (wrapper && wrapper.classList.contains('crp-code-wrapper')) {
        wrapper.appendChild(button);
    }
}

// Initialize on document ready
jQuery(document).ready(function ($) {
    crpHandleThumbnailStyleChange();
});
