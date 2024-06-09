<?php
/**
 * Sidebar
 *
 * @package WebberZone\Contextual_Related_Posts
 */

use function WebberZone\Contextual_Related_Posts\crp_freemius;

?>
<div class="postbox-container">
	<?php if ( ! crp_freemius()->is_paying() ) { ?>
	<div id="pro-upgrade-banner">
		<div class="inside" style="text-align: center">
			<p><a href="https://webberzone.com/plugins/contextual-related-posts/pro/" target="_blank"><img src="<?php echo esc_url( CRP_PLUGIN_URL . 'includes/admin/images/crp-pro-banner.png' ); ?>" alt="<?php esc_html_e( 'Contextual Related Posts Pro - Buy now!', 'contextual-related-posts' ); ?>" width="300" height="300" style="max-width: 100%;" /></a></p>
			<p><?php esc_html_e( 'OR' ); ?></p>
			<p><a href="https://wzn.io/donate-crp" target="_blank"><img src="<?php echo esc_url( CRP_PLUGIN_URL . 'includes/admin/images/support.webp' ); ?>" alt="<?php esc_html_e( 'Support the development - Send us a donation today.', 'contextual-related-posts' ); ?>" width="300" height="169" style="max-width: 100%;" /></a></p>
		</div>
	</div>
	<?php } ?>

	<div id="qlinksdiv" class="postbox meta-box-sortables">
		<h2 class='hndle metabox-holder'><span><?php esc_html_e( 'Quick links', 'contextual-related-posts' ); ?></span></h2>

		<div class="inside">
			<div id="quick-links">
				<ul class="subsub">
					<li>
						<a href="https://webberzone.com/plugins/contextual-related-posts/" target="_blank"><?php esc_html_e( 'Contextual Related Posts homepage', 'contextual-related-posts' ); ?></a>
					</li>

					<li>
						<a href="https://webberzone.com/support/product/contextual-related-posts/" target="_blank"><?php esc_html_e( 'Knowledge Base', 'contextual-related-posts' ); ?></a>
					</li>
					<?php if ( ! crp_freemius()->is_paying() ) { ?>
					<li>
						<a href="https://wordpress.org/support/plugin/contextual-related-posts/" target="_blank"><?php esc_html_e( 'Support', 'contextual-related-posts' ); ?></a>
					</li>
					<?php } else { ?>
					<li>
						<a href="https://webberzone.com/request-support/" target="_blank"><?php esc_html_e( 'Support', 'contextual-related-posts' ); ?></a>
					</li>
					<?php } ?>
					<li>
						<a href="https://wordpress.org/support/plugin/contextual-related-posts/reviews/" target="_blank"><?php esc_html_e( 'Reviews', 'contextual-related-posts' ); ?></a>
					</li>
					<li>
						<a href="https://github.com/webberzone/contextual-related-posts" target="_blank"><?php esc_html_e( 'Github repository', 'contextual-related-posts' ); ?></a>
					</li>
					<li>
						<a href="https://ajaydsouza.com/" target="_blank"><?php esc_html_e( "Ajay's blog", 'contextual-related-posts' ); ?></a>
					</li>
				</ul>
			</div>
		</div><!-- /.inside -->
	</div><!-- /.postbox -->

	<div id="pluginsdiv" class="postbox meta-box-sortables">
		<h2 class='hndle metabox-holder'><span><?php esc_html_e( 'WebberZone plugins', 'contextual-related-posts' ); ?></span></h2>

		<div class="inside">
			<div id="quick-links">
				<ul class="subsub">
					<li><a href="https://webberzone.com/plugins/top-10/" target="_blank"><?php esc_html_e( 'Top 10', 'contextual-related-posts' ); ?></a></li>
					<li><a href="https://webberzone.com/plugins/better-search/" target="_blank"><?php esc_html_e( 'Better Search', 'contextual-related-posts' ); ?></a></li>
					<li><a href="https://webberzone.com/plugins/knowledgebase/" target="_blank"><?php esc_html_e( 'Knowledge Base', 'contextual-related-posts' ); ?></a></li>
					<li><a href="https://webberzone.com/plugins/add-to-all/" target="_blank"><?php esc_html_e( 'Snippetz', 'contextual-related-posts' ); ?></a></li>
					<li><a href="https://webberzone.com/webberzone-followed-posts/" target="_blank"><?php esc_html_e( 'Followed Posts', 'contextual-related-posts' ); ?></a></li>
					<li><a href="https://webberzone.com/plugins/popular-authors/" target="_blank"><?php esc_html_e( 'Popular Authors', 'contextual-related-posts' ); ?></a></li>
					<li><a href="https://webberzone.com/plugins/autoclose/" target="_blank"><?php esc_html_e( 'Auto Close', 'contextual-related-posts' ); ?></a></li>
				</ul>
			</div>
		</div><!-- /.inside -->
	</div><!-- /.postbox -->	

</div>

<div class="postbox-container">
	<div id="followdiv" class="postbox meta-box-sortables">
		<h2 class='hndle'><span><?php esc_html_e( 'Follow us', 'contextual-related-posts' ); ?></span></h2>

		<div class="inside" style="text-align: center">
		<a href="https://x.com/webberzone/" target="_blank"><img src="<?php echo esc_url( CRP_PLUGIN_URL . 'includes/admin/images/x.png' ); ?>" width="100" height="100"></a>
			<a href="https://facebook.com/webberzone/" target="_blank"><img src="<?php echo esc_url( CRP_PLUGIN_URL . 'includes/admin/images/fb.png' ); ?>" width="100" height="100"></a>
		</div><!-- /.inside -->
	</div><!-- /.postbox -->
</div>