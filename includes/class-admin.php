<?php
    namespace DiviNationKit;

    if ( !defined( 'ABSPATH' ) ) {
    exit;
    }

    class Admin {

    const SLUG   = 'divinationkit';
    const CAP    = 'manage_options';
    const NONCE  = 'divinationkit_save';
    const ACTION = 'divinationkit_save_settings';

    private $settings;
    private $features;

    private $pages = array(
        'dashboard' => 'Dashboard',
        'general'   => 'General',
        'tools'     => 'Tools',
        'addons'    => 'Addons',
        'about'     => 'About',
    );

    // Custom menu svg icon instead of dashicon form assest/image/icon.svg
    private $icon_svg = DIVINATIONKIT_URL . 'admin/assets/images/icon.svg';

    public function __construct( Settings $settings, Features $features ) {
        $this->settings = $settings;
        $this->features = $features;

        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_post_' . self::ACTION, array( $this, 'handle_save' ) );
        add_action( 'wp_ajax_' . self::ACTION, array( $this, 'handle_save_ajax' ) );
        add_action( 'current_screen', array( $this, 'suppress_admin_notices' ) );
        add_filter( 'plugin_action_links_' . DIVINATIONKIT_BASENAME, array( $this, 'plugin_action_links' ) );
    }

    public function plugin_action_links( $links ) {
        $url     = admin_url( 'admin.php?page=' . self::SLUG );
        $links[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'divinationkit' ) . '</a>';

        return $links;
    }

    public function register_menu(): void {
        add_menu_page(
            __( 'DiviNationKit', 'divinationkit' ),
            __( 'DiviNationKit', 'divinationkit' ),
            self::CAP,
            self::SLUG,
            array( $this, 'render_dashboard' ),
            'dashicons-admin-generic',
            59
        );

        foreach ( $this->pages as $slug => $label ) {
            $page_slug = 'dashboard' === $slug ? self::SLUG : self::SLUG . '-' . $slug;
            add_submenu_page(
                self::SLUG,
                $label,
                $label,
                self::CAP,
                $page_slug,
                array( $this, 'render_' . $slug )
            );
        }
    }

    public function enqueue_assets( $hook ): void {
        if ( !$this->is_our_screen() ) {
            return;
        }

        wp_enqueue_style(
            'divinationkit-admin',
            DIVINATIONKIT_URL . 'admin/assets/admin.css',
            array(),
            DIVINATIONKIT_VERSION
        );

        wp_enqueue_script(
            'divinationkit-admin',
            DIVINATIONKIT_URL . 'admin/assets/admin.js',
            array(),
            DIVINATIONKIT_VERSION,
            true
        );
    }

    public function suppress_admin_notices( $screen ): void {
        if ( !$screen || !$this->is_our_screen() ) {
            return;
        }
        remove_all_actions( 'admin_notices' );
        remove_all_actions( 'all_admin_notices' );
        remove_all_actions( 'user_admin_notices' );
        remove_all_actions( 'network_admin_notices' );
    }

    private function is_our_screen(): bool {
        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( $screen && is_string( $screen->id ) && strpos( $screen->id, self::SLUG ) !== false ) {
            return true;
        }
        // fallback for early hooks (admin_enqueue_scripts gets hook suffix string)
        $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        return $page === self::SLUG || strpos( $page, self::SLUG . '-' ) === 0;
    }

    public function handle_save(): void {
        if ( !current_user_can( self::CAP ) ) {
            wp_die( esc_html__( 'You do not have permission to do this.', 'divinationkit' ) );
        }
        check_admin_referer( self::NONCE );

        $this->save_settings_from_post();

        $redirect = isset( $_POST['_wp_http_referer'] )
        ? esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) )
        : admin_url( 'admin.php?page=' . self::SLUG );

        $redirect = add_query_arg( 'dnk-saved', '1', $redirect );
        wp_safe_redirect( $redirect );
        exit;
    }

    public function handle_save_ajax(): void {
        if ( !current_user_can( self::CAP ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to do this.', 'divinationkit' ) ), 403 );
        }
        if ( !check_ajax_referer( self::NONCE, '_wpnonce', false ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed. Please reload the page and try again.', 'divinationkit' ) ), 400 );
        }

        $this->save_settings_from_post();

        wp_send_json_success( array( 'message' => __( 'Settings saved.', 'divinationkit' ) ) );
    }

    private function save_settings_from_post(): void {
        $section = isset( $_POST['dnk_section'] ) ? sanitize_key( wp_unslash( $_POST['dnk_section'] ) ) : '';
        $raw     = isset( $_POST['dnk'] ) && is_array( $_POST['dnk'] ) ? wp_unslash( $_POST['dnk'] ) : array();

        switch ( $section ) {
        case 'general':
        case 'design': // legacy section name
            $this->save_design( $raw );
            break;
        case 'tools':
            $this->save_tools( $raw );
            break;
        }
    }

    private function save_design( array $raw ): void {
        $design = array(
            'menu_height'     => isset( $raw['menu_height'] )
            ? max( 10, min( 120, (int) $raw['menu_height'] ) )
            : 20,
            'container_width' => isset( $raw['container_width'] )
            ? max( 50, min( 100, (int) $raw['container_width'] ) )
            : 90,
        );
        $this->settings->save_section( 'design', $design );
    }

    private function save_tools( array $raw ): void {
        foreach ( $this->features->all() as $feature ) {
            $id    = $feature->get_id();
            $input = isset( $raw[$id] ) && is_array( $raw[$id] ) ? $raw[$id] : array();
            $this->settings->save_tool( $id, $feature->sanitize( $input ) );
        }
    }

    /**
     * Shared save bar (button + inline notice slot).
     *
     * Use `position` => 'top' to emit the class that drops bottom-margin to 0
     * and pulls the bar tight against the first card.
     */
    public static function render_action_bar( string $position = 'bottom' ): void {
        $class = 'dnk-actions' . ( $position === 'top' ? ' dnk-actions-top' : '' );
        ?>
        <div class="<?php echo esc_attr( $class ); ?>">
            <button type="submit" class="dnk-btn dnk-btn-primary"><?php esc_html_e( 'Save changes', 'divinationkit' ); ?></button>
            <span class="dnk-action-notice" role="status" aria-live="polite"></span>
        </div>
        <?php
            }

                /**
                 * Shared markup for the color + alpha picker control.
                 */
                public static function render_color_field( string $name, string $value, string $default, string $id ): void {
                    $value = $value !== '' ? $value : $default;
                ?>
		<div class="dnk-color-control" data-default="<?php echo esc_attr( $default ); ?>">
			<input type="hidden"
			       name="<?php echo esc_attr( $name ); ?>"
			       value="<?php echo esc_attr( $value ); ?>"
			       class="dnk-color-input" />
			<span class="dnk-color-swatch" aria-hidden="true">
				<span class="dnk-color-swatch-fill" style="background: <?php echo esc_attr( $value ); ?>;"></span>
			</span>
			<input type="color"
			       id="<?php echo esc_attr( $id ); ?>"
			       class="dnk-color-hex"
			       value="#01564d" />
			<div class="dnk-color-alpha-row">
				<input type="range"
				       class="dnk-color-alpha"
				       min="0" max="100" step="1" value="100"
				       aria-label="<?php esc_attr_e( 'Opacity', 'divinationkit' ); ?>" />
				<span class="dnk-color-alpha-val">100%</span>
			</div>
		</div>
		<?php
            }

                /* ------------------ Renderers ------------------ */

                public function render_dashboard(): void {
                    $this->render_page( 'dashboard', __( 'Dashboard', 'divinationkit' ), __( 'Quick overview of DiviNationKit features and helpful resources.', 'divinationkit' ) );
                }

                public function render_general(): void {
                    $this->render_page( 'general', __( 'General', 'divinationkit' ), __( 'Site-wide layout tokens and helpful shortcodes you can drop anywhere.', 'divinationkit' ) );
                }

                public function render_tools(): void {
                    $this->render_page( 'tools', __( 'Tools', 'divinationkit' ), __( 'Toggle features on or off. Enabled tools expose their own settings.', 'divinationkit' ) );
                }

                public function render_addons(): void {
                    $this->render_page( 'addons', __( 'Addons', 'divinationkit' ), __( 'Free and premium companions for your DiviNationKit setup.', 'divinationkit' ) );
                }

                public function render_about(): void {
                    $this->render_page( 'about', __( 'About', 'divinationkit' ), __( 'About the plugin and the team behind DiviNationKit.', 'divinationkit' ) );
                }

                private function render_page( string $slug, string $title, string $description ): void {
                    $header_path = DIVINATIONKIT_DIR . 'admin/views/header.php';
                    $body_path   = DIVINATIONKIT_DIR . 'admin/views/page-' . $slug . '.php';
                    $footer_path = DIVINATIONKIT_DIR . 'admin/views/footer.php';

                    $page     = $slug;
                    $settings = $this->settings;
                    $features = $this->features;
                    $saved    = isset( $_GET['dnk-saved'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

                    include $header_path;
                    include $body_path;
                    include $footer_path;
                }
        }
