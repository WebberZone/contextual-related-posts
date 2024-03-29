<?php
/**
 * Sidebar
 *
 * @package WebberZone\Contextual_Related_Posts
 */

?>
<div class="postbox-container">
	<div id="pro-upgrade-banner">
		<div class="inside" style="text-align: center">
			<a href="https://webberzone.com/plugins/contextual-related-posts/pro/" target="_blank"><img src="<?php echo esc_url( CRP_PLUGIN_URL . 'includes/admin/images/contextual-related-posts-pro-banner.jpg' ); ?>" alt="<?php esc_html_e( 'Contextual Related Posts Pro - Coming soon. Sign up to find out more', 'contextual-related-posts' ); ?>" width="300" height="300" style="max-width: 100%;" /></a>
		</div>
	</div>

	<div id="donatediv" class="postbox meta-box-sortables">
		<h2 class='hndle'><span><?php esc_attr_e( 'Support the development', 'contextual-related-posts' ); ?></span></h2>

		<div class="inside" style="text-align: center">
			<div id="donate-form">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_xclick">
					<input type="hidden" name="business" value="donate@ajaydsouza.com">
					<input type="hidden" name="lc" value="IN">
					<input type="hidden" name="item_name" value="<?php esc_html_e( 'Donation for Contextual Related Posts', 'contextual-related-posts' ); ?>">
					<input type="hidden" name="item_number" value="bsearch_plugin_settings">
					<strong><?php esc_html_e( 'Enter amount in USD', 'contextual-related-posts' ); ?></strong>: <input name="amount" value="15.00" size="6" type="text"><br />
					<input type="hidden" name="currency_code" value="USD">
					<input type="hidden" name="button_subtype" value="services">
					<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
					<input type="image" src="<?php echo esc_url( CRP_PLUGIN_URL . 'includes/admin/images/paypal_donate_button.webp' ); ?>" border="0" name="submit" alt="<?php esc_html_e( 'Send your donation to the author of', 'contextual-related-posts' ); ?> Contextual Related Posts">
				</form>
			</div><!-- /#donate-form -->
		</div><!-- /.inside -->
	</div><!-- /.postbox -->

	<div id="qlinksdiv" class="postbox meta-box-sortables">
		<h2 class='hndle metabox-holder'><span><?php esc_html_e( 'Quick links', 'contextual-related-posts' ); ?></span></h2>

		<div class="inside">
			<div id="quick-links">
				<ul class="subsub">
					<li>
						<a href="https://webberzone.com/plugins/contextual-related-posts/"><?php esc_html_e( 'Contextual Related Posts plugin homepage', 'contextual-related-posts' ); ?></a>
					</li>

					<li>
						<a href="https://wordpress.org/plugins/contextual-related-posts/faq/"><?php esc_html_e( 'FAQ', 'contextual-related-posts' ); ?></a>
					</li>

					<li>
						<a href="https://wordpress.org/support/plugin/contextual-related-posts/"><?php esc_html_e( 'Support', 'contextual-related-posts' ); ?></a>
					</li>

					<li>
						<a href="https://wordpress.org/support/plugin/contextual-related-posts/reviews/"><?php esc_html_e( 'Reviews', 'contextual-related-posts' ); ?></a>
					</li>

					<li>
						<a href="https://github.com/ajaydsouza/contextual-related-posts"><?php esc_html_e( 'Github repository', 'contextual-related-posts' ); ?></a>
					</li>

					<li>
						<a href="https://webberzone.com/plugins/"><?php esc_html_e( 'Other plugins', 'contextual-related-posts' ); ?></a>
					</li>

					<li>
						<a href="https://webberzone.com/"><?php esc_html_e( "Ajay's blog", 'contextual-related-posts' ); ?></a>
					</li>
				</ul>
			</div>
		</div><!-- /.inside -->
	</div><!-- /.postbox -->
</div>

<div class="postbox-container">
	<div id="followdiv" class="postbox meta-box-sortables">
		<h2 class='hndle'><span><?php esc_html_e( 'Follow me', 'contextual-related-posts' ); ?></span></h2>

		<div class="inside" style="text-align: center">
		<a href="https://twitter.com/webberzone/" target="_blank"><img src="<?php echo esc_url( CRP_PLUGIN_URL . 'includes/admin/images/x.png' ); ?>" width="100" height="100"></a>
			<a href="https://facebook.com/webberzone/" target="_blank"><img src="<?php echo esc_url( CRP_PLUGIN_URL . 'includes/admin/images/fb.png' ); ?>" width="100" height="100"></a>
		</div><!-- /.inside -->
	</div><!-- /.postbox -->
</div>