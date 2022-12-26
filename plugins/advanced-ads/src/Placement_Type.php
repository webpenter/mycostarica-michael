<?php

namespace Advanced_Ads;

/**
 * Class wrapper for placement types array.
 *
 * @property-read string                 $title
 * @property-read string                 $description
 * @property-read string                 $image
 * @property-read float                  $order
 * @property-read Placement_Type_Options $options
 */
class Placement_Type extends \ArrayObject {

	/**
	 * Placement type title.
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Placement type description.
	 *
	 * @var string
	 */
	private $description = '';

	/**
	 * Admin UI image src.
	 *
	 * @var string
	 */
	private $image = '';

	/**
	 * Admin UI order for new placements.
	 *
	 * @var float
	 */
	private $order;

	/**
	 * A class to resolve the placement type options.
	 *
	 * @var Placement_Type_Options
	 */
	private $options;

	/**
	 * The placement type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Assign simple placement definitions to properties.
	 * Instantiate Placement_Type_Options class.
	 *
	 * @param string $type                 The type of placement.
	 * @param array  $placement_definition The definition options for the placement.
	 */
	public function __construct( $type, array $placement_definition ) {
		$this->type = $type;

		if ( array_key_exists( 'title', $placement_definition ) ) {
			$this->title = $placement_definition['title'];
		}

		if ( array_key_exists( 'description', $placement_definition ) ) {
			$this->description = $placement_definition['description'];
		}

		if ( array_key_exists( 'image', $placement_definition ) ) {
			$this->image = $placement_definition['image'];
		}

		if ( array_key_exists( 'order', $placement_definition ) ) {
			$this->order = (float) $placement_definition['order'];
		}

		if ( ! array_key_exists( 'options', $placement_definition ) || ! is_array( $placement_definition['options'] ) ) {
			$placement_definition['options'] = [];
		}

		$this->options = new Placement_Type_Options( $placement_definition['options'] );

		parent::__construct( $placement_definition );
	}

	/**
	 * Magic catch to have readonly properties.
	 *
	 * @param string $name The name of the requested property.
	 *
	 * @return mixed
	 * @noinspection MagicMethodsValidityInspection -- no setter as we only want readonly properties
	 */
	public function __get( $name ) {
		if ( property_exists( $this, $name ) ) {
			return $this->{$name};
		}

		return null;
	}

	/**
	 * Check if the provided ad type is allowed (or at least not excluded).
	 * If an ad type is both allowed and forbidden, the allow-list takes precedence.
	 *
	 * @param string $type Ad type.
	 *
	 * @return bool
	 */
	public function is_ad_type_allowed( $type ) {
		return $this->is_abstract_allowed( $type, 'ad' );
	}

	/**
	 * Check if the provided ad group type is allowed.
	 *
	 * @param string $type Ad group type.
	 *
	 * @return bool
	 */
	public function is_group_type_allowed( $type ) {
		return $this->is_abstract_allowed( $type, 'group' );
	}

	/**
	 * Abstraction of comparing whether type is allowed or excluded.
	 *
	 * @param string $type  Specific Advanced_Ads_Ad::$type or Advanced_Ads_Ad_Group::$type.
	 * @param string $class Overall classification, one of `ad` or `group`.
	 *
	 * @return bool
	 */
	private function is_abstract_allowed( $type, $class ) {
		$allowed = $this->options->offsetGet( 'allowed_' . $class . '_types' );

		if ( $allowed === null ) {
			return ! in_array( $type, $this->options->offsetGet( 'excluded_' . $class . '_types' ), true );
		}

		return in_array( $type, $allowed, true );
	}
}
