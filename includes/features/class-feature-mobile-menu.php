<?php
namespace DiviNationKit\Features;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Mobile_Menu extends Feature {

    public function get_id(): string {
        return 'mobile_menu';
    }

    public function get_label(): string {
        return __( 'Mobile Dropdown Menu', 'divinationkit' );
    }

    public function get_description(): string {
        return __( 'Adds a per-item toggler to the Divi mobile nav so each parent menu item expands its submenu independently with a slide animation.', 'divinationkit' );
    }

    public function get_defaults(): array {
        return array(
            'mobile_menu_height'           => 70,
            'icon_color'                   => '#01564d',
            'active_icon_color'            => '#ffffff',
            'icon_background_color'        => '#01564d',
            'active_icon_background_color' => '#01564d',
            'toggler_roundness'            => 0,
        );
    }

    public function get_fields(): array {
        return array(
            array(
                'id'    => 'icon_color',
                'type'  => 'color',
                'label' => __( 'Icon color', 'divinationkit' ),
                'help'  => __( 'Drives both the icon color and the toggler background (a 10% tint of this color is used when closed).', 'divinationkit' ),
            ),
            array(
                'id'    => 'icon_background_color',
                'type'  => 'color',
                'label' => __( 'Icon Background Color', 'divinationkit' ),
                'help'  => __( 'Set the background color for the mobile menu toggler.', 'divinationkit' ),
            ),
            array(
                'id'    => 'active_icon_color',
                'type'  => 'color',
                'label' => __( 'Active Icon Color', 'divinationkit' ),
                'help'  => __( 'Set the color for the active icon.', 'divinationkit' ),
            ),
            array(
                'id'    => 'active_icon_background_color',
                'type'  => 'color',
                'label' => __( 'Active Icon Background Color', 'divinationkit' ),
                'help'  => __( 'Set the background color for the active icon.', 'divinationkit' ),
            ),
            array(
                'id'    => 'toggler_roundness',
                'type'  => 'range',
                'label' => __( 'Toggler Roundness', 'divinationkit' ),
                'help'  => __( 'Adjust the roundness of the mobile menu toggler.', 'divinationkit' ),
                'min'   => 0,
                'max'   => 500,
                'step'  => 1,
            ),
            array(
                'id'    => 'mobile_menu_height',
                'type'  => 'range',
                'label' => __( 'Mobile Menu Height', 'divinationkit' ),
                'help'  => __( 'When the header is sticky and the menu is active and have many menu item and can visible all of them. ', 'divinationkit' ),
                'min'   => 0,
                'max'   => 1000,
                'step'  => 1,
            ),
        );
    }

    public function has_stylesheet(): bool {
        return true;
    }

    public function has_script(): bool {
        return true;
    }

    public function script_deps(): array {
        return array( 'jquery' );
    }

    public function inline_css( array $values ): string {
        $icon_color                   = $values['icon_color'] ?? '#01564d';
        $active_icon_color            = $values['active_icon_color'] ?? '#ffffff';
        $active_icon_background_color = $values['active_icon_background_color'] ?? '#01564d';
        $icon_bg                      = $values['icon_background_color'] ?? '#01564d';
        $menu_height                  = $values['mobile_menu_height'] ?? 70;
        $toggler_roundness            = $values['toggler_roundness'] ?? 0;

        return sprintf(
            ':root{--dnk-mobile-icon:%s;--dnk-mobile-icon-bg:%s; --dnk-mobile-menu-height:%svh; --dnk-mobile-toggler-roundness:%spx; --dnk-mobile-active-icon:%s; --dnk-mobile-active-icon-bg:%s;}',
            esc_attr( $icon_color ),
            esc_attr( $icon_bg ),
            esc_attr( $menu_height ),
            esc_attr( $toggler_roundness ),
            esc_attr( $active_icon_color ),
            esc_attr( $active_icon_background_color )
        );
    }
}
