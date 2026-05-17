<?php
namespace DiviNationKit\Features;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Feature {

    abstract public function get_id(): string;

    abstract public function get_label(): string;

    public function get_description(): string {
        return '';
    }

    /**
     * Default values for this feature, including 'enabled'.
     */
    abstract public function get_defaults(): array;

    /**
     * Field definitions used to render the Tools tab card.
     *
     * Each field: array(
     *   'id'    => string,
     *   'type'  => 'color' | 'number' | 'text' | 'range',
     *   'label' => string,
     *   'min'   => int (optional, for number/range),
     *   'max'   => int (optional),
     *   'step'  => int (optional),
     *   'unit'  => string (optional, suffix label),
     *   'help'  => string (optional),
     * )
     *
     * @return array<int,array<string,mixed>>
     */
    abstract public function get_fields(): array;

    /**
     * Sanitize submitted values for this feature.
     */
    public function sanitize( array $input ): array {
        $out            = array();
        $out['enabled'] = !empty( $input['enabled'] ) ? 1 : 0;

        foreach ( $this->get_fields() as $field ) {
            $id    = $field['id'];
            $value = $input[$id] ?? null;

            switch ( $field['type'] ) {
            case 'color':
                $out[$id] = $this->sanitize_color( (string) $value, $this->get_defaults()[$id] ?? '' );
                break;
            case 'number':
                $out[$id] = is_numeric( $value ) ? (float) $value : (float) ( $this->get_defaults()[$id] ?? 0 );
                if ( isset( $field['min'] ) ) {
                    $out[$id] = max( (float) $field['min'], $out[$id] );
                }
                if ( isset( $field['max'] ) ) {
                    $out[$id] = min( (float) $field['max'], $out[$id] );
                }
                break;
            case 'range':
                $out[$id] = is_numeric( $value ) ? (float) $value : (float) ( $this->get_defaults()[$id] ?? 0 );
                if ( isset( $field['min'] ) ) {
                    $out[$id] = max( (float) $field['min'], $out[$id] );
                }
                if ( isset( $field['max'] ) ) {
                    $out[$id] = min( (float) $field['max'], $out[$id] );
                }
                break;
            case 'text':
            default:
                $out[$id] = sanitize_text_field( (string) $value );
                break;
            }
        }

        return $out;
    }

    /**
     * Inline CSS to inject when feature is enabled.
     * Use CSS custom properties scoped to :root so the static stylesheets can read them.
     */
    public function inline_css( array $values ): string {
        return '';
    }

    /**
     * Whether to enqueue the bundled CSS for this feature.
     */
    public function has_stylesheet(): bool {
        return false;
    }

    /**
     * Whether to enqueue the bundled JS for this feature.
     */
    public function has_script(): bool {
        return false;
    }

    /**
     * Filename (without extension) under assets/css and assets/js.
     */
    public function asset_handle(): string {
        return 'divinationkit-' . $this->get_id();
    }

    public function asset_basename(): string {
        return $this->get_id();
    }

    public function script_deps(): array {
        return array();
    }

    protected function sanitize_color( string $value, string $fallback ): string {
        $value = trim( $value );
        if ( $value === '' ) {
            return $fallback;
        }
        // Allow #rgb / #rrggbb / #rrggbbaa
        if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $value ) ) {
            return strtolower( $value );
        }
        // Allow rgb()/rgba()
        if ( preg_match( '/^rgba?\([\d\s\.,%\/]+\)$/i', $value ) ) {
            return $value;
        }

        return $fallback;
    }
}
