<?php
/**
 * Represents the sidebar view for the administration dashboard.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2015 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div id="donatediv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'contextual-related-posts' ); ?>"><br /></div>
	<h3 class='hndle'><span><?php esc_html_e( 'Support the development', 'contextual-related-posts' ); ?></span></h3>
	<div class="inside">
	<div id="donate-form">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="business" value="donate@ajaydsouza.com">
		<input type="hidden" name="lc" value="IN">
		<input type="hidden" name="item_name" value="<?php esc_attr_e( 'Donation for Contextual Related Posts', 'contextual-related-posts' ); ?>">
		<input type="hidden" name="item_number" value="crp_plugin_settings">
		<strong><?php esc_html_e( 'Enter amount in USD:', 'contextual-related-posts' ); ?></strong> <input name="amount" value="10.00" size="6" type="text"><br />
		<input type="hidden" name="currency_code" value="USD">
		<input type="hidden" name="button_subtype" value="services">
		<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php esc_attr_e( 'Send your donation to the author of', 'contextual-related-posts' ); ?> Contextual Related Posts?">
		<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
	</div>
	</div>
</div>

<div id="qlinksdiv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'contextual-related-posts' ); ?>"><br /></div>
	<h3 class='hndle'><span><?php esc_html_e( 'Quick links', 'contextual-related-posts' ); ?></span></h3>
	<div class="inside">
	<div id="quick-links">
		<ul>
			<li><a href="https://webberzone.com/"><?php esc_html_e( 'WebberZone', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://webberzone.com/plugins/contextual-related-posts/"><?php esc_html_e( 'Plugin homepage', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://wordpress.org/plugins/contextual-related-posts/faq/"><?php esc_html_e( 'FAQ', 'contextual-related-posts' ); ?></a></li>
			<li><a href="http://wordpress.org/support/plugin/contextual-related-posts"><?php esc_html_e( 'Support', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://wordpress.org/support/view/plugin-reviews/contextual-related-posts"><?php esc_html_e( 'Reviews', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://github.com/WebberZone/contextual-related-posts"><?php esc_html_e( 'Github repository', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://webberzone.com/plugins/"><?php esc_html_e( 'Other plugins', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://webberzone.com/"><?php esc_html_e( "Ajay's blog", 'contextual-related-posts' ); ?></a></li>
		</ul>
	</div>
	</div>
</div>

<div id="followdiv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'contextual-related-posts' ); ?>"><br /></div>
	<h3 class='hndle'><span><?php esc_html_e( 'Follow us', 'contextual-related-posts' ); ?></span></h3>
	<div class="inside">
		<a href="https://facebook.com/webberzone/" target="_blank"><img src="<?php echo esc_url( CRP_PLUGIN_URL . '/admin/images/fb.png' ); ?>" width="100" height="100" /></a>
		<a href="https://twitter.com/webberzonewp/" target="_blank"><img src="<?php echo esc_url( CRP_PLUGIN_URL . '/admin/images/twitter.jpg' ); ?>" width="100" height="100" /></a>
	</div>
</div>

