/* global ajaxurl, WZTomSelectSettings, jQuery, TomSelect */

(function ($) {
    'use strict';

    function normalizeOption(item) {
        if (!item || typeof item !== 'object') {
            return null;
        }

        const value = item.id || item.value || '';
        const text = item.name || item.text || value;

        if (!value && !text) {
            return null;
        }

        return { value: String(value), text: String(text) };
    }

    function getEndpointOptions(settings, endpoint) {
        if (!settings || !endpoint) {
            return [];
        }

        const endpointOptions = settings[endpoint];

        if (!Array.isArray(endpointOptions)) {
            return [];
        }

        return endpointOptions
            .map(normalizeOption)
            .filter(Boolean);
    }

    function isTaxonomyEndpoint(endpoint) {
        const key = typeof endpoint === 'string' ? endpoint : '';
        return key === 'category' || key === 'post_tag' || key.includes('tax');
    }

    function initTomSelect(root) {
        const scope = (root && typeof root.querySelectorAll === 'function') ? root : document;
        const elements = scope.querySelectorAll('.ts_autocomplete');

        elements.forEach(function (element) {
            if (element.tomselect) {
                return;
            }

            const prefix = element.getAttribute('data-wp-prefix') || 'WZ';
            const settingsKey = `${prefix}TomSelectSettings`;
            const settings = window[settingsKey]
                || window.WZTomSelectSettings
                || window.freemkitTomSelectSettings
                || {};

            if (!settings || typeof settings !== 'object') {
                return;
            }

            const action = element.getAttribute('data-wp-action') || settings.action;
            const nonce = element.getAttribute('data-wp-nonce') || settings.nonce;
            const endpoint = element.getAttribute('data-wp-endpoint') || settings.endpoint;
            const strings = settings.strings || {};

            const formattedOptions = getEndpointOptions(settings, endpoint);

            const savedIds = element.value.split(',').map(id => id.trim()).filter(Boolean);
            const taxonomyEndpoint = isTaxonomyEndpoint(endpoint);

            // For taxonomy endpoints, add saved values as options so Tom Select can display them
            if (taxonomyEndpoint && savedIds.length > 0) {
                const savedOptions = savedIds.map(savedValue => {
                    // Extract term name from formatted string "Name (taxonomy:id)"
                    const match = savedValue.match(/^(.*)\s+\(.*:\d+\)$/);
                    const termName = match ? match[1] : savedValue;
                    return { value: savedValue, text: termName };
                });

                // Merge saved options with existing options, avoiding duplicates
                const allOptions = [...formattedOptions];
                savedOptions.forEach(savedOption => {
                    if (!allOptions.some(opt => opt.value === savedOption.value)) {
                        allOptions.push(savedOption);
                    }
                });

                // Replace formattedOptions with merged options
                formattedOptions.length = 0;
                formattedOptions.push(...allOptions);
            }

            // For non-taxonomy endpoints, add saved values as options so Tom Select can display them.
            if (!taxonomyEndpoint && savedIds.length > 0) {
                savedIds.forEach(savedValue => {
                    if (!formattedOptions.some(opt => opt.value === savedValue)) {
                        formattedOptions.push({ value: savedValue, text: savedValue });
                    }
                });
            }

            // Get any custom config from data attributes
            let customConfig = {};
            const configAttr = element.getAttribute('data-ts-config');

            if (configAttr) {
                try {
                    customConfig = JSON.parse(configAttr);
                } catch (e) {
                    console.error('Error parsing custom config:', configAttr, e);
                }
            }

            // Default config
            const defaultConfig = {
                plugins: ['dropdown_input', 'clear_button', 'remove_button'],
                valueField: 'value',
                labelField: 'text',
                searchField: ['text', 'value'],
                options: formattedOptions,
                items: savedIds,
                persist: true,
                createOnBlur: false,
                create: false,
                render: {
                    no_results: (data, escape) => {
                        const template = strings.no_results || 'No results for "%s"';
                        return `<div class="no-results">${template.replace('%s', escape(data.input))}</div>`;
                    },
                    option: (data, escape) => {
                        // For taxonomy endpoints, display only the formatted value to avoid duplication
                        if (taxonomyEndpoint) {
                            return `<div>${escape(data.value)}</div>`;
                        }
                        // Avoid showing "value (value)" when value and text are identical.
                        if (data.text === data.value) {
                            return `<div>${escape(data.value)}</div>`;
                        }
                        return `<div>${escape(data.text)} (${escape(data.value)})</div>`;
                    },
                    item: (data, escape) => {
                        // For taxonomy endpoints, display only the formatted value to avoid duplication
                        if (taxonomyEndpoint) {
                            return `<div>${escape(data.value)}</div>`;
                        }
                        // Avoid showing "value (value)" when value and text are identical.
                        if (data.text === data.value) {
                            return `<div>${escape(data.value)}</div>`;
                        }
                        return `<div>${escape(data.text)} (${escape(data.value)})</div>`;
                    }
                },
                load: function (query, callback) {
                    if (!query.length) {
                        callback();
                        return;
                    }

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: action,
                            nonce: nonce,
                            q: query,
                            endpoint: endpoint
                        },
                        error: function () {
                            callback();
                        },
                        success: function (res) {
                            if (res.success && res.data && Array.isArray(res.data.items)) {
                                callback(
                                    res.data.items
                                        .map(normalizeOption)
                                        .filter(Boolean)
                                );
                            } else if (res.success && Array.isArray(res.data)) {
                                callback(
                                    res.data
                                        .map(normalizeOption)
                                        .filter(Boolean)
                                );
                            } else {
                                callback();
                            }
                        }
                    });
                }
            };

            // Merge default config with custom config
            const finalConfig = { ...defaultConfig, ...customConfig };

            // Initialize Tom Select with merged config
            try {
                new TomSelect(element, finalConfig);
            } catch (error) {
                console.error('Tom Select initialization error:', error);
            }
        });
    }

    window.WZInitTomSelect = initTomSelect;

    document.addEventListener('wz:repeater-item-added', function (event) {
        const detail = event && event.detail ? event.detail : {};
        initTomSelect(detail.container || document);
    });

    $(document).ready(function () {
        initTomSelect(document);
    });
})(jQuery);
