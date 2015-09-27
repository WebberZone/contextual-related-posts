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

<div id="donatediv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
  <h3 class='hndle'><span><?php _e( 'Support the development', 'contextual-related-posts' ); ?></span></h3>
  <div class="inside">
	<div id="donate-form">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="business" value="donate@ajaydsouza.com">
		<input type="hidden" name="lc" value="IN">
		<input type="hidden" name="item_name" value="<?php _e( 'Donation for Contextual Related Posts', 'contextual-related-posts' ); ?>">
		<input type="hidden" name="item_number" value="crp_plugin_settings">
		<strong><?php _e( 'Enter amount in USD:', 'contextual-related-posts' ); ?></strong> <input name="amount" value="10.00" size="6" type="text"><br />
		<input type="hidden" name="currency_code" value="USD">
		<input type="hidden" name="button_subtype" value="services">
		<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php _e( 'Send your donation to the author of', 'contextual-related-posts' ); ?> Contextual Related Posts?">
		<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
	</div>
  </div>
</div>

<div id="followdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
  <h3 class='hndle'><span><?php _e( 'Follow me', 'contextual-related-posts' ); ?></span></h3>
  <div class="inside">
	<div id="twitter">
		<div style="text-align:center"><a href="https://twitter.com/WebberZoneWP" class="twitter-follow-button" data-show-count="false" data-size="large" data-dnt="true">Follow @WebberZoneWP</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>
	</div>
	<div id="facebook">
		<div id="fb-root"></div>
		<script>
		//<![CDATA[
			(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=458036114376706";
			fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		//]]>
		</script>
		<div class="fb-page" data-href="https://www.facebook.com/WebberZone" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false" data-show-posts="false"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/WebberZone"><a href="https://www.facebook.com/WebberZone">WebberZone</a></blockquote></div></div>
	</div>
  </div>
</div>

<div id="qlinksdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
  <h3 class='hndle'><span><?php _e( 'Quick links', 'contextual-related-posts' ); ?></span></h3>
  <div class="inside">
    <div id="quick-links">
		<ul>
			<li><a href="https://webberzone.com/plugins/contextual-related-posts/"><?php _e( 'Plugin homepage', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://wordpress.org/plugins/contextual-related-posts/faq/"><?php _e( 'FAQ', 'contextual-related-posts' ); ?></a></li>
			<li><a href="http://wordpress.org/support/plugin/contextual-related-posts"><?php _e( 'Support', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://wordpress.org/support/view/plugin-reviews/contextual-related-posts"><?php _e( 'Reviews', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://github.com/WebberZone/contextual-related-posts"><?php _e( 'Github repository', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://webberzone.com/plugins/"><?php _e( 'Other plugins', 'contextual-related-posts' ); ?></a></li>
			<li><a href="https://webberzone.com/"><?php _e( "Ajay's blog", 'contextual-related-posts' ); ?></a></li>
		</ul>
    </div>
  </div>
</div>
