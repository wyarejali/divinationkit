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

	<?php \DiviNationKit\Admin::render_action_bar( 'top' ); ?>

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
						$is_info = ( $field['type'] ?? '' ) === 'info';
						$value   = $is_info ? '' : ( $values[ $field['id'] ] ?? '' );
						$name    = $is_info ? '' : sprintf( 'dnk[%s][%s]', $id, $field['id'] );
						$fid     = $is_info ? '' : sprintf( 'dnk-%s-%s', $id, str_replace( '_', '-', $field['id'] ) );
						$default = $is_info ? '' : ( $feature->get_defaults()[ $field['id'] ] ?? '' );
						?>
						<div class="dnk-field <?php echo $is_info ? 'dnk-field-info' : ''; ?>">
						<?php if ( $is_info ) : ?>
							<div class="dnk-info-block">
								<?php if ( ! empty( $field['label'] ) ) : ?>
									<strong class="dnk-info-title"><?php echo esc_html( $field['label'] ); ?></strong>
								<?php endif; ?>
								<?php if ( ! empty( $field['help'] ) ) : ?>
									<p class="dnk-info-body"><?php echo wp_kses_post( $field['help'] ); ?></p>
								<?php endif; ?>
							</div>
						<?php else : ?>
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

							<?php elseif ( 'select' === $field['type'] ) :
								$opts = (array) ( $field['options'] ?? array() );
								?>
								<select id="<?php echo esc_attr( $fid ); ?>"
								        name="<?php echo esc_attr( $name ); ?>"
								        class="dnk-select">
									<?php foreach ( $opts as $opt_value => $opt_label ) : ?>
										<option value="<?php echo esc_attr( $opt_value ); ?>" <?php selected( (string) $value, (string) $opt_value ); ?>>
											<?php echo esc_html( $opt_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>

							<?php elseif ( 'multicheck' === $field['type'] ) :
								$opts     = (array) ( $field['options'] ?? array() );
								$selected = is_array( $value ) ? array_map( 'strval', $value ) : array();
								?>
								<div class="dnk-multicheck">
									<?php foreach ( $opts as $opt_value => $opt_label ) :
										$cid = $fid . '-' . sanitize_key( (string) $opt_value );
										?>
										<label class="dnk-multicheck-row" for="<?php echo esc_attr( $cid ); ?>">
											<input type="checkbox"
											       id="<?php echo esc_attr( $cid ); ?>"
											       name="<?php echo esc_attr( $name ); ?>[]"
											       value="<?php echo esc_attr( $opt_value ); ?>"
											       <?php checked( in_array( (string) $opt_value, $selected, true ) ); ?> />
											<span><?php echo esc_html( $opt_label ); ?></span>
										</label>
									<?php endforeach; ?>
								</div>

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
						<?php endif; /* is_info */ ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endforeach; ?>

	<?php \DiviNationKit\Admin::render_action_bar(); ?>
</form>
