// Function to clear the cache.
function crpClearCache() {
	/**** since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php ****/
	jQuery.post(ajaxurl, {
		action: 'crp_clear_cache',
		security: crp_admin_data.security
	}, function (response, textStatus, jqXHR) {
		alert(response.message);
	}, 'json');
}
