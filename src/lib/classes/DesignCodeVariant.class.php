<?php

/**
 * Data class representing an design code variant.
 */
class DesignCodeVariant {

	// single value attributes
	public $id;
	public $edition_id;
	public $code;
	public $requires_exceedence_probability;
	public $display_order;

	public function __construct ($id=null, $edition_id=null, $code=null,
				$requires_exceedence_probability=null, $display_order=null) {
		$this->id = $id;
		$this->edition_id = $edition_id;
		$this->code = $code;
		$this->requires_exceedence_probability = 
				$requires_exceedence_probability;
		$this->display_order = $display_order;
	}

	public static function fromArray (&$p) {
		return new DesignCodeVariant($p['id'], $p['edition_id'], $p['code'],
					$p['requires_exceedence_probability'], $p['display_order']);
	}

	public function toArray () {
		return array('id' => $this->id,
					'edition_id' => $this->edition_id,
					'code' => $this->code,
					'requires_exceedence_probability' =>
							$this->requires_exceedence_probability,
					'display_order' => $this->display_order);
	}

}

?>