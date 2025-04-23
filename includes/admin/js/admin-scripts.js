// Function to clear the cache.
function crpClearCache() {
    // Get the button
    const button = document.getElementById('cache_clear');

    // Disable button and change text
    if (button) {
        button.disabled = true;
        button.textContent = crp_admin_data.clearing_cache;
    }

    // Send AJAX request
    jQuery.post(ajaxurl, {
        action: 'crp_clear_cache',
        security: crp_admin_data.security
    }, function (response, textStatus, jqXHR) {
        // Re-enable button and restore text
        if (button) {
            button.disabled = false;
            button.textContent = crp_admin_data.clear_cache;
        }

        // Show response message
        alert(response.message);
    }, 'json').fail(function () {
        // Re-enable button and restore text on error
        if (button) {
            button.disabled = false;
            button.textContent = crp_admin_data.clear_cache;
        }
    });

    return false;
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
