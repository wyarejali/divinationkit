<?php
namespace DiviNationKit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

	const OPTION_KEY = 'divinationkit_settings';

	private $cache = null;

	public function defaults(): array {
		return array(
			'design' => array(
				'menu_height'     => 20,
				'container_width' => 90,
			),
			'tools'  => array(),
		);
	}

	public function all(): array {
		if ( null === $this->cache ) {
			$stored      = get_option( self::OPTION_KEY, array() );
			$stored      = is_array( $stored ) ? $stored : array();
			$this->cache = $this->merge_recursive( $this->defaults(), $stored );
		}
		return $this->cache;
	}

	public function design(): array {
		$all = $this->all();
		return $all['design'];
	}

	public function tool( string $feature_id ): array {
		$all = $this->all();
		return isset( $all['tools'][ $feature_id ] ) && is_array( $all['tools'][ $feature_id ] )
			? $all['tools'][ $feature_id ]
			: array();
	}

	public function is_tool_enabled( string $feature_id ): bool {
		$tool = $this->tool( $feature_id );
		return ! empty( $tool['enabled'] );
	}

	public function save( array $values ): void {
		update_option( self::OPTION_KEY, $values );
		$this->cache = null;
	}

	public function save_section( string $section, array $values ): void {
		$all = $this->all();
		if ( ! isset( $all[ $section ] ) || ! is_array( $all[ $section ] ) ) {
			$all[ $section ] = array();
		}
		$all[ $section ] = array_replace( $all[ $section ], $values );
		$this->save( $all );
	}

	public function save_tool( string $feature_id, array $values ): void {
		$all = $this->all();
		if ( ! isset( $all['tools'] ) || ! is_array( $all['tools'] ) ) {
			$all['tools'] = array();
		}
		$all['tools'][ $feature_id ] = $values;
		$this->save( $all );
	}

	public function seed_defaults_for_feature( string $feature_id, array $defaults ): void {
		$all = $this->all();
		if ( ! isset( $all['tools'][ $feature_id ] ) ) {
			$all['tools'][ $feature_id ] = $defaults;
			$this->save( $all );
		}
	}

	private function merge_recursive( array $defaults, array $values ): array {
		foreach ( $values as $key => $value ) {
			if ( is_array( $value ) && isset( $defaults[ $key ] ) && is_array( $defaults[ $key ] ) ) {
				$defaults[ $key ] = $this->merge_recursive( $defaults[ $key ], $value );
			} else {
				$defaults[ $key ] = $value;
			}
		}
		return $defaults;
	}
}
