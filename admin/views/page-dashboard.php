<?php
/**
 * @var \DiviNationKit\Settings $settings
 * @var \DiviNationKit\Features $features
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$enabled_count = 0;
foreach ( $features->all() as $f ) {
	if ( $settings->is_tool_enabled( $f->get_id() ) ) {
		$enabled_count++;
	}
}
$total       = count( $features->all() );
$design      = $settings->design();
?>

<div class="dnk-grid dnk-grid-3">
	<div class="dnk-card dnk-stat">
		<div class="dnk-stat-num"><?php echo esc_html( $enabled_count . '/' . $total ); ?></div>
		<div class="dnk-stat-label"><?php esc_html_e( 'Tools enabled', 'divinationkit' ); ?></div>
	</div>
	<div class="dnk-card dnk-stat">
		<div class="dnk-stat-num"><?php echo esc_html( DIVINATIONKIT_VERSION ); ?></div>
		<div class="dnk-stat-label"><?php esc_html_e( 'Installed version', 'divinationkit' ); ?></div>
	</div>
	<div class="dnk-card dnk-stat">
		<div class="dnk-stat-num"><?php echo esc_html( $design['menu_height'] . 'px' ); ?></div>
		<div class="dnk-stat-label"><?php esc_html_e( 'Menu line height', 'divinationkit' ); ?></div>
	</div>
</div>

<div class="dnk-grid dnk-grid-2">
	<section class="dnk-card">
		<h2 class="dnk-card-title"><?php esc_html_e( 'Get started', 'divinationkit' ); ?></h2>
		<ol class="dnk-steps">
			<li>
				<strong><?php esc_html_e( 'Dial in layout', 'divinationkit' ); ?></strong>
				<p><?php esc_html_e( 'Set the menu line height and mobile container width on the Design tab. These flow into the live site automatically.', 'divinationkit' ); ?></p>
				<a class="dnk-btn dnk-btn-link" href="<?php echo esc_url( admin_url( 'admin.php?page=divinationkit-general' ) ); ?>"><?php esc_html_e( 'Open General →', 'divinationkit' ); ?></a>
			</li>
			<li>
				<strong><?php esc_html_e( 'Enable the tools you want', 'divinationkit' ); ?></strong>
				<p><?php esc_html_e( 'The mobile menu and Expandable shortcode are on by default. Toggle them off if a project does not need them.', 'divinationkit' ); ?></p>
				<a class="dnk-btn dnk-btn-link" href="<?php echo esc_url( admin_url( 'admin.php?page=divinationkit-tools' ) ); ?>"><?php esc_html_e( 'Open Tools →', 'divinationkit' ); ?></a>
			</li>
			<li>
				<strong><?php esc_html_e( 'Use the [expandable] shortcode', 'divinationkit' ); ?></strong>
				<p><code>[expandable words="40"]<?php esc_html_e( 'Your long copy here.', 'divinationkit' ); ?>[/expandable]</code></p>
			</li>
		</ol>
	</section>

	<section class="dnk-card">
		<h2 class="dnk-card-title"><?php esc_html_e( 'Resources', 'divinationkit' ); ?></h2>
		<ul class="dnk-links">
			<li><a href="https://www.divinationkit.com/blog/" target="_blank" rel="noopener"><?php esc_html_e( 'Divi tutorials &amp; blog', 'divinationkit' ); ?></a></li>
			<li><a href="https://www.divinationkit.com/shop/" target="_blank" rel="noopener"><?php esc_html_e( 'Premium Divi products', 'divinationkit' ); ?></a></li>
			<li><a href="https://github.com/wyarejali/Divi-Child-Theme-By-DiviNationKit" target="_blank" rel="noopener"><?php esc_html_e( 'Source on GitHub', 'divinationkit' ); ?></a></li>
		</ul>
	</section>
</div>
