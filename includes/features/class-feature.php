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
     * Subscribe to WordPress hooks. Called by the Plugin bootstrap only when
     * the feature is enabled. Default is a no-op — features that only emit
     * CSS/JS via the Assets pipeline don't need to override this.
     */
    public function register(): void {}

    /**
     * Whether this feature should load its CSS/JS/inline-CSS on the current
     * frontend request. Called by Assets after is_tool_enabled() passes.
     *
     * Default: true (load everywhere). Features like Reading Progress Bar
     * override this to only load on selected post types.
     *
     * @param array $values Saved feature values (whatever Settings::tool() returns).
     */
    public function should_load_on_frontend( array $values ): bool {
        return true;
    }

    /**
     * Sanitize submitted values for this feature.
     */
    public function sanitize( array $input ): array {
        $out            = array();
        $out['enabled'] = !empty( $input['enabled'] ) ? 1 : 0;

        foreach ( $this->get_fields() as $field ) {
            if ( ( $field['type'] ?? '' ) === 'info' || empty( $field['id'] ) ) {
                continue;
            }
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
            case 'select':
                $allowed   = array_keys( (array) ( $field['options'] ?? array() ) );
                $candidate = is_string( $value ) || is_int( $value ) ? (string) $value : '';
                $default   = (string) ( $this->get_defaults()[$id] ?? ( $allowed[0] ?? '' ) );
                $out[$id]  = in_array( $candidate, $allowed, true ) ? $candidate : $default;
                break;
            case 'multicheck':
                $allowed  = array_keys( (array) ( $field['options'] ?? array() ) );
                $vals     = is_array( $value ) ? array_map( 'strval', $value ) : array();
                $out[$id] = array_values( array_intersect( $vals, $allowed ) );
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
