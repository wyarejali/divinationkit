<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="dnk-grid dnk-grid-2">
	<section class="dnk-card">
		<h2 class="dnk-card-title"><?php esc_html_e( 'About DiviNationKit', 'divinationkit' ); ?></h2>
		<p>
			<?php esc_html_e( 'DiviNationKit is a small toolkit that takes the design tweaks Divi builders rebuild on every project and lifts them into a single, manageable plugin. No more hardcoding mobile menu togglers or pasting Read More scripts into every theme.', 'divinationkit' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Everything is opt-in. Toggle a tool off and its CSS and JavaScript never load — so disabled features cost nothing on the frontend.', 'divinationkit' ); ?>
		</p>
	</section>

	<section class="dnk-card">
		<h2 class="dnk-card-title"><?php esc_html_e( 'Credits', 'divinationkit' ); ?></h2>
		<ul class="dnk-links">
			<li><strong><?php esc_html_e( 'Author', 'divinationkit' ); ?>:</strong> Wyarej Ali</li>
			<li><strong><?php esc_html_e( 'Website', 'divinationkit' ); ?>:</strong> <a href="https://www.divinationkit.com/" target="_blank" rel="noopener">divinationkit.com</a></li>
			<li><strong><?php esc_html_e( 'License', 'divinationkit' ); ?>:</strong> GPLv2 or later</li>
			<li><strong><?php esc_html_e( 'Version', 'divinationkit' ); ?>:</strong> <?php echo esc_html( DIVINATIONKIT_VERSION ); ?></li>
		</ul>
	</section>
</div>
