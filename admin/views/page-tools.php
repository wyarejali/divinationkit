<?php
/**
 * @var \DiviNationKit\Settings $settings
 * @var \DiviNationKit\Features $features
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="dnk-form">
	<?php wp_nonce_field( \DiviNationKit\Admin::NONCE ); ?>
	<input type="hidden" name="action" value="<?php echo esc_attr( \DiviNationKit\Admin::ACTION ); ?>" />
	<input type="hidden" name="dnk_section" value="tools" />

	<?php
	foreach ( $features->all() as $feature ) :
		$id      = $feature->get_id();
		$values  = array_replace( $feature->get_defaults(), $settings->tool( $id ) );
		$enabled = ! empty( $values['enabled'] );
		?>
		<section class="dnk-card dnk-feature <?php echo $enabled ? 'is-enabled' : ''; ?>" data-feature="<?php echo esc_attr( $id ); ?>">
			<header class="dnk-feature-head">
				<button type="button" class="dnk-collapse-toggle" aria-expanded="false">
					<span class="dnk-chevron" aria-hidden="true"></span>
				</button>

				<div class="dnk-feature-info">
					<h2 class="dnk-card-title"><?php echo esc_html( $feature->get_label() ); ?></h2>
					<?php if ( $feature->get_description() ) : ?>
						<p class="dnk-feature-desc"><?php echo esc_html( $feature->get_description() ); ?></p>
					<?php endif; ?>
				</div>

				<label class="dnk-switch">
					<input type="hidden" name="dnk[<?php echo esc_attr( $id ); ?>][enabled]" value="0" />
					<input type="checkbox"
					       name="dnk[<?php echo esc_attr( $id ); ?>][enabled]"
					       value="1"
					       class="dnk-feature-toggle"
					       <?php checked( $enabled ); ?> />
					<span class="dnk-switch-slider"></span>
				</label>
			</header>

			<div class="dnk-feature-body" hidden>
				<div class="dnk-fields-grid">
					<?php foreach ( $feature->get_fields() as $field ) :
						$value   = $values[ $field['id'] ] ?? '';
						$name    = sprintf( 'dnk[%s][%s]', $id, $field['id'] );
						$fid     = sprintf( 'dnk-%s-%s', $id, str_replace( '_', '-', $field['id'] ) );
						$default = $feature->get_defaults()[ $field['id'] ] ?? '';
						?>
						<div class="dnk-field">
							<label class="dnk-label" for="<?php echo esc_attr( $fid ); ?>">
								<?php echo esc_html( $field['label'] ); ?>
								<?php if ( ! empty( $field['unit'] ) ) : ?>
									<span class="dnk-label-suffix"><?php echo esc_html( $field['unit'] ); ?></span>
								<?php endif; ?>
							</label>

							<?php if ( 'color' === $field['type'] ) :
								\DiviNationKit\Admin::render_color_field( $name, (string) $value, (string) $default, $fid );
							elseif ( 'range' === $field['type'] ) : ?>
								<div class="dnk-range-row">
									<input type="range"
									       id="<?php echo esc_attr( $fid ); ?>-range"
									       min="<?php echo esc_attr( $field['min'] ?? 0 ); ?>"
									       max="<?php echo esc_attr( $field['max'] ?? 100 ); ?>"
									       step="<?php echo esc_attr( $field['step'] ?? 1 ); ?>"
									       value="<?php echo esc_attr( $value ); ?>"
									       data-bind="<?php echo esc_attr( $fid ); ?>" />
									<input type="number"
									       id="<?php echo esc_attr( $fid ); ?>"
									       name="<?php echo esc_attr( $name ); ?>"
									       min="<?php echo esc_attr( $field['min'] ?? 0 ); ?>"
									       max="<?php echo esc_attr( $field['max'] ?? 100 ); ?>"
									       step="<?php echo esc_attr( $field['step'] ?? 1 ); ?>"
									       value="<?php echo esc_attr( $value ); ?>"
									       data-bind="<?php echo esc_attr( $fid ); ?>-range"
									       class="dnk-number" />
								</div>

							<?php elseif ( 'number' === $field['type'] ) : ?>
								<input type="number"
								       id="<?php echo esc_attr( $fid ); ?>"
								       name="<?php echo esc_attr( $name ); ?>"
								       min="<?php echo esc_attr( $field['min'] ?? 0 ); ?>"
								       max="<?php echo esc_attr( $field['max'] ?? 9999 ); ?>"
								       step="<?php echo esc_attr( $field['step'] ?? 1 ); ?>"
								       value="<?php echo esc_attr( $value ); ?>"
								       class="dnk-number" />

							<?php else : ?>
								<input type="text"
								       id="<?php echo esc_attr( $fid ); ?>"
								       name="<?php echo esc_attr( $name ); ?>"
								       value="<?php echo esc_attr( $value ); ?>"
								       class="dnk-text" />
							<?php endif; ?>

							<?php if ( ! empty( $field['help'] ) ) : ?>
								<p class="dnk-help"><?php echo esc_html( $field['help'] ); ?></p>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endforeach; ?>

	<div class="dnk-actions">
		<button type="submit" class="dnk-btn dnk-btn-primary"><?php esc_html_e( 'Save changes', 'divinationkit' ); ?></button>
	</div>
</form>
