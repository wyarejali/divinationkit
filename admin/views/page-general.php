<?php
    /**
 * @var \DiviNationKit\Settings $settings
 */
    if ( !defined( 'ABSPATH' ) ) {
    exit;
    }

    $design     = $settings->design();
    $shortcodes = array(
        array(
            'tag'  => 'current_year',
            'desc' => __( 'Outputs the current 4-digit year. Drop it in your footer to never edit it again.', 'divinationkit' ),
        ),
        array(
            'tag'  => 'copyright',
            'desc' => __( 'Copyright line. Optional attrs: start="2018" (year range) and name="My Site". Example: [copyright start="2018" name="DiviNationKit"] → © 2018–2026 DiviNationKit.', 'divinationkit' ),
        ),
        array(
            'tag'  => 'reading_time',
            'desc' => __( 'Estimated reading time for the current post. Optional attrs: wpm="200" and suffix="min read". Returns empty outside the post loop unless you pass words="450".', 'divinationkit' ),
        ),
        array(
            'tag'  => 'site_url',
            'desc' => __( 'The site\'s home URL. Optional path="/contact" to append a relative path.', 'divinationkit' ),
        ),
        array(
            'tag'  => 'user_first_name',
            'desc' => __( 'Logged-in user\'s first name. Optional fallback="there" for visitors who aren\'t logged in.', 'divinationkit' ),
        ),
    );
?>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="dnk-form">
	<?php wp_nonce_field( \DiviNationKit\Admin::NONCE ); ?>
	<input type="hidden" name="action" value="<?php echo esc_attr( \DiviNationKit\Admin::ACTION ); ?>" />
	<input type="hidden" name="dnk_section" value="general" />

	<?php \DiviNationKit\Admin::render_action_bar( 'top' ); ?>

	<section class="dnk-card">
		<h2 class="dnk-card-title"><?php esc_html_e( 'Layout', 'divinationkit' ); ?></h2>
		<p class="dnk-card-help"><?php esc_html_e( 'Site-wide spacing and rhythm. New layout options drop into this grid automatically.', 'divinationkit' ); ?></p>

		<div class="dnk-fields-grid">
			<div class="dnk-field">
				<label class="dnk-label" for="dnk-menu-height">
					<?php esc_html_e( 'Menu line height', 'divinationkit' ); ?>
					<span class="dnk-label-suffix">px</span>
				</label>
				<div class="dnk-range-row">
					<input type="range"
					       id="dnk-menu-height-range"
					       min="10" max="120" step="1"
					       value="<?php echo esc_attr( $design['menu_height'] ); ?>"
					       data-bind="dnk-menu-height" />
					<input type="number"
					       id="dnk-menu-height"
					       name="dnk[menu_height]"
					       min="10" max="120" step="1"
					       value="<?php echo esc_attr( $design['menu_height'] ); ?>"
					       data-bind="dnk-menu-height-range"
					       class="dnk-number" />
				</div>
				<p class="dnk-help"><?php esc_html_e( 'Sets line-height for desktop top-level nav links.', 'divinationkit' ); ?></p>
			</div>

			<div class="dnk-field">
				<label class="dnk-label" for="dnk-container-width">
					<?php esc_html_e( 'Mobile container width', 'divinationkit' ); ?>
					<span class="dnk-label-suffix">%</span>
				</label>
				<div class="dnk-range-row">
					<input type="range"
					       id="dnk-container-width-range"
					       min="50" max="100" step="1"
					       value="<?php echo esc_attr( $design['container_width'] ); ?>"
					       data-bind="dnk-container-width" />
					<input type="number"
					       id="dnk-container-width"
					       name="dnk[container_width]"
					       min="50" max="100" step="1"
					       value="<?php echo esc_attr( $design['container_width'] ); ?>"
					       data-bind="dnk-container-width-range"
					       class="dnk-number" />
				</div>
				<p class="dnk-help"><?php esc_html_e( 'Applied below 500px to .et_pb_row and header .container.', 'divinationkit' ); ?></p>
			</div>
		</div>
	</section>

	<section class="dnk-card">
	<h2 class="dnk-card-title"><?php esc_html_e( 'Helpful shortcodes', 'divinationkit' ); ?></h2>
	<p class="dnk-card-help"><?php esc_html_e( 'Paste any of these into a Text module, an HTML widget, or a post. They render the moment the page loads.', 'divinationkit' ); ?></p>

	<ul class="dnk-shortcodes">
		<?php foreach ( $shortcodes as $sc ):
                $tag = '[' . $sc['tag'] . ']';
        ?>
			<li class="dnk-shortcode-row">
				<code class="dnk-shortcode-code"><?php echo esc_html( $tag ); ?></code>
				<button type="button"
				        class="dnk-btn dnk-btn-secondary dnk-copy"
				        data-copy="<?php echo esc_attr( $tag ); ?>"
				        data-copied-label="<?php esc_attr_e( 'Copied!', 'divinationkit' ); ?>">
					<?php esc_html_e( 'Copy', 'divinationkit' ); ?>
				</button>
				<span class="dnk-shortcode-desc"><?php echo esc_html( $sc['desc'] ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</section>

	<?php \DiviNationKit\Admin::render_action_bar(); ?>
</form>


