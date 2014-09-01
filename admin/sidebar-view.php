<?php
/**
 * Represents the sidebar view for the administration dashboard.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      http://ajaydsouza.com
 * @copyright 2009-2014 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div id="donatediv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
  <h3 class='hndle'><span><?php _e( 'Support the development', CRP_LOCAL_NAME ); ?></span></h3>
  <div class="inside">
	<div id="donate-form">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="business" value="donate@ajaydsouza.com">
		<input type="hidden" name="lc" value="IN">
		<input type="hidden" name="item_name" value="<?php _e( 'Donation for Contextual Related Posts', CRP_LOCAL_NAME ); ?>">
		<input type="hidden" name="item_number" value="crp_plugin_settings">
		<strong><?php _e( 'Enter amount in USD:', CRP_LOCAL_NAME ); ?></strong> <input name="amount" value="10.00" size="6" type="text"><br />
		<input type="hidden" name="currency_code" value="USD">
		<input type="hidden" name="button_subtype" value="services">
		<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php _e( 'Send your donation to the author of', CRP_LOCAL_NAME ); ?> Contextual Related Posts?">
		<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
	</div>
  </div>
</div>

<div id="followdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
  <h3 class='hndle'><span><?php _e( 'Follow me', CRP_LOCAL_NAME ); ?></span></h3>
  <div class="inside">
	<div id="follow-us">
		<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fajaydsouzacom&amp;width=292&amp;height=62&amp;colorscheme=light&amp;show_faces=false&amp;border_color&amp;stream=false&amp;header=true&amp;appId=113175385243" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:62px;" allowTransparency="true"></iframe>
		<div style="text-align:center"><a href="https://twitter.com/ajaydsouza" class="twitter-follow-button" data-show-count="false" data-size="large" data-dnt="true">Follow @ajaydsouza</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>
	</div>
  </div>
</div>

<div id="qlinksdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
  <h3 class='hndle'><span><?php _e( 'Quick links', CRP_LOCAL_NAME ); ?></span></h3>
  <div class="inside">
    <div id="quick-links">
		<ul>
			<li><a href="http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/"><?php _e( 'Contextual Related Posts plugin page', CRP_LOCAL_NAME ); ?></a></li>
			<li><a href="https://wordpress.org/plugins/contextual-related-posts/faq/"><?php _e( 'FAQ', CRP_LOCAL_NAME ); ?></a></li>
			<li><a href="http://wordpress.org/support/plugin/contextual-related-posts"><?php _e( 'Support', CRP_LOCAL_NAME ); ?></a></li>
			<li><a href="https://wordpress.org/support/view/plugin-reviews/contextual-related-posts"><?php _e( 'Reviews', CRP_LOCAL_NAME ); ?></a></li>
			<li><a href="https://github.com/ajaydsouza/contextual-related-posts"><?php _e( 'Github repository', CRP_LOCAL_NAME ); ?></a></li>
			<li><a href="http://ajaydsouza.com/wordpress/plugins/"><?php _e( 'Other plugins', CRP_LOCAL_NAME ); ?></a></li>
			<li><a href="http://ajaydsouza.com/"><?php _e( "Ajay's blog", CRP_LOCAL_NAME ); ?></a></li>
		</ul>
    </div>
  </div>
</div>
