<?php
    /**
 * @var string $page
 * @var string $title
 * @var string $description
 * @var bool   $saved
 */
    if ( !defined( 'ABSPATH' ) ) {
    exit;
    }

    $nav = array(
    'dashboard' => __( 'Dashboard', 'divinationkit' ),
    'general'   => __( 'General', 'divinationkit' ),
    'tools'     => __( 'Tools', 'divinationkit' ),
    'addons'    => __( 'Addons', 'divinationkit' ),
    'about'     => __( 'About', 'divinationkit' ),
    );
?>
<div class="dnk-wrap">
	<header class="dnk-hero">
		<div class="dnk-hero-inner">
			<div class="dnk-hero-wrapper">
				<div class="dnk-logo">
					<img class="dnk-logo-img"
					     src="<?php echo esc_url( DIVINATIONKIT_URL . 'admin/assets/images/icon.svg' ); ?>"
					     alt="<?php esc_attr_e( 'DiviNationKit', 'divinationkit' ); ?>" />
				</div>
				<div class="dnk-hero-text">
					<h1 class="dnk-title"><?php echo esc_html( $title ); ?> <span class="dnk-stage">Beta</span></h1>
					<p class="dnk-subtitle"><?php echo esc_html( $description ); ?></p>
				</div>

			</div>
			<div class="dnk-hero-meta">
				<span class="dnk-version">v<?php echo esc_html( DIVINATIONKIT_VERSION ); ?></span>
			</div>
		</div>

		<nav class="dnk-tabs" aria-label="<?php esc_attr_e( 'DiviNationKit sections', 'divinationkit' ); ?>">
			<?php foreach ( $nav as $nav_slug => $nav_label ):
                    $page_slug = 'dashboard' === $nav_slug ? 'divinationkit' : 'divinationkit-' . $nav_slug;
                    $is_active = $nav_slug === $page;
            ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $page_slug ) ); ?>"
				   class="dnk-tab <?php echo $is_active ? 'is-active' : ''; ?>">
					<?php echo esc_html( $nav_label ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
	</header>

	<main class="dnk-content">
		<?php if ( $saved ): ?>
			<div class="dnk-notice dnk-notice-success" role="status">
				<?php esc_html_e( 'Settings saved.', 'divinationkit' ); ?>
			</div>
		<?php endif; ?>
