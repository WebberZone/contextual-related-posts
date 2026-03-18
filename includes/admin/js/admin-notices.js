/**
 * Admin notice dismissal handler.
 *
 * Reads configs pushed into window.adminNoticesConfigs by each plugin instance
 * via wp_add_inline_script. Each config object contains:
 *   - prefix  {string} Plugin prefix (matches data-notice-prefix attribute).
 *   - action  {string} AJAX action name.
 *   - nonce   {string} Nonce for the AJAX request.
 */
jQuery(document).ready(function ($) {
	var configs = window.adminNoticesConfigs || [];

	configs.forEach(function (config) {
		$('.notice[data-notice-prefix="' + config.prefix + '"]').on('click', '.notice-dismiss', function () {
			var $notice = $(this).closest('.notice');

			$.post(ajaxurl, {
				action: config.action,
				notice_id: $notice.data('notice-id'),
				dismiss_time: $notice.data('dismiss-time'),
				nonce: config.nonce
			});
		});
	});
});
