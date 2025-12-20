/* global ajaxurl, WZTomSelectSettings, jQuery, TomSelect */

(function ($) {
    'use strict';

    function initTomSelect() {
        const elements = document.querySelectorAll('.ts_autocomplete');

        elements.forEach(function (element) {
            const prefix = element.getAttribute('data-wp-prefix') || 'WZ';
            const settingsKey = `${prefix}TomSelectSettings`;
            const settings = window[settingsKey] || WZTomSelectSettings;

            const action = element.getAttribute('data-wp-action') || settings.action;
            const nonce = element.getAttribute('data-wp-nonce') || settings.nonce;
            const endpoint = element.getAttribute('data-wp-endpoint') || settings.endpoint;
            const forms = settings.forms;
            const tags = settings.tags;
            const custom_fields = settings.custom_fields;
            const strings = settings.strings;

            const options = endpoint === 'forms' ? forms : (endpoint === 'tags' ? tags : (endpoint === 'custom_fields' ? custom_fields : []));

            if (!options || !Array.isArray(options)) {
                console.error('Invalid options for endpoint:', endpoint);
                return;
            }

            const formattedOptions = options.map(item => ({ value: item.id, text: item.name }));

            const savedIds = element.value.split(',').map(id => id.trim()).filter(Boolean);

            // For taxonomy endpoints, add saved values as options so Tom Select can display them
            if ((endpoint === 'category' || endpoint === 'post_tag' || endpoint.includes('tax')) && savedIds.length > 0) {
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
            if (!(endpoint === 'category' || endpoint === 'post_tag' || endpoint.includes('tax')) && savedIds.length > 0) {
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
                    // console.log('Parsed custom config:', customConfig); // Debug log
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
                    no_results: (data, escape) => `<div class="no-results">${strings.no_results.replace('%s', escape(data.input))}</div>`,
                    option: (data, escape) => {
                        // For taxonomy endpoints, display only the formatted value to avoid duplication
                        if (endpoint === 'category' || endpoint === 'post_tag' || endpoint.includes('tax')) {
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
                        if (endpoint === 'category' || endpoint === 'post_tag' || endpoint.includes('tax')) {
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
                            if (res.success && Array.isArray(res.data)) {
                                callback(res.data);
                            } else {
                                callback();
                            }
                        }
                    });
                }
            };

            // Merge default config with custom config
            const finalConfig = { ...defaultConfig, ...customConfig };
            // console.log('Final config:', finalConfig); // Debug log

            // Initialize Tom Select with merged config
            try {
                new TomSelect(element, finalConfig);
            } catch (error) {
                console.error('Tom Select initialization error:', error);
            }
        });
    }

    $(document).ready(initTomSelect);
})(jQuery);
