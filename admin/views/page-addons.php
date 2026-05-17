<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$addons = array(
	array(
		'name'        => __( 'DiviNationKit Forms', 'divinationkit' ),
		'tagline'     => __( 'Lightweight contact &amp; lead forms built for Divi.', 'divinationkit' ),
		'price'       => 'free',
		'install_url' => '#',
	),
	array(
		'name'        => __( 'DiviNationKit Pro', 'divinationkit' ),
		'tagline'     => __( 'Advanced layouts, animation packs, and premium support.', 'divinationkit' ),
		'price'       => 'paid',
		'install_url' => 'https://www.divinationkit.com/shop/',
	),
);
?>

<div class="dnk-grid dnk-grid-2">
	<?php foreach ( $addons as $addon ) : ?>
		<article class="dnk-card dnk-addon">
			<div class="dnk-addon-head">
				<div class="dnk-addon-thumb" aria-hidden="true">
					<?php echo esc_html( strtoupper( substr( $addon['name'], 0, 1 ) ) ); ?>
				</div>
				<div>
					<h2 class="dnk-card-title"><?php echo esc_html( $addon['name'] ); ?></h2>
					<span class="dnk-badge dnk-badge-<?php echo esc_attr( $addon['price'] ); ?>">
						<?php echo $addon['price'] === 'free'
							? esc_html__( 'Free', 'divinationkit' )
							: esc_html__( 'Premium', 'divinationkit' ); ?>
					</span>
				</div>
			</div>
			<p class="dnk-addon-tagline"><?php echo wp_kses_post( $addon['tagline'] ); ?></p>

			<?php if ( $addon['price'] === 'free' ) : ?>
				<a class="dnk-btn dnk-btn-primary" href="<?php echo esc_url( $addon['install_url'] ); ?>">
					<?php esc_html_e( 'Install', 'divinationkit' ); ?>
				</a>
			<?php else : ?>
				<a class="dnk-btn dnk-btn-secondary" href="<?php echo esc_url( $addon['install_url'] ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Get Pro', 'divinationkit' ); ?>
				</a>
			<?php endif; ?>
		</article>
	<?php endforeach; ?>
</div>
