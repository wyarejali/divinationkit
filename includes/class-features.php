<?php
namespace DiviNationKit;

use DiviNationKit\Features\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Features {

	/** @var Feature[] */
	private $features = array();

	public function register( Feature $feature ): void {
		$this->features[ $feature->get_id() ] = $feature;
	}

	/** @return Feature[] */
	public function all(): array {
		return $this->features;
	}

	public function get( string $id ): ?Feature {
		return $this->features[ $id ] ?? null;
	}
}
