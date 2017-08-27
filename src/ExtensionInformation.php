<?php

namespace GooseStudio\WpUpdatesAPI;

/**
 * Class ExtensionInformation
 * @package GooseStudio\WpUpdatesAPI
 * @property string
 * @property array sections [description,installation,faq,screenshots,changelog,reviews,other_notes]
 * @property string name
 * @property string slug
 * @property string homepage
 * @property string short_description
 * @property float version
 * @property float tested
 * @property array author [url,url,...]
 * @property float requires
 * @property int rating
 * @property int num_ratings
 * @property int downloaded
 * @property int active_installs
 * @property array banners [low,high]
 * @property string last_updated
 * @property string added
 * @property array tags
 * @property float compatibility
 * @property string donate_link
 * @property bool external
 * @property array contributors ['name' => 'url']

 */
class ExtensionInformation {
	/**
	 * @var array
	 */
	private $properties;

	/**
	 * ExtensionInformation constructor.
	 */
	public function __construct() {
		$this->properties = [
			'sections'          => [],
			'name'              => '',
			'slug'              => '',
			'homepage'          => '',
			'short_description' => '',
			'version'           => '',
			'tested'            => '',
			'author'            => '',
			'requires'          => '',
			'rating'            => null,
			'num_ratings'       => null,
			'downloaded'        => null,
			'active_installs'   => null,
			'banners'           => [
				'low'  => '',
				'high' => ''
			],
			'last_updated'      => '',
			'added'             => '',
			'tags'              => [],
			'compatibility'     => '',
			'donate_link'       => '',
			'external'          => true,
			'contributors'      => []
		];
	}

	/**
	 * @param $property
	 * @param $value
	 *
	 * @throws \OutOfBoundsException
	 * @throws \UnexpectedValueException
	 */
	public function __set( $property, $value ) {
		if ( $this->has_property( $property ) ) {
			$this->properties[ $property ] = $value;
		} else {
			throw new \OutOfBoundsException("$property is not a property of class");
		}
	}

	/**
	 * @param string $property
	 *
	 * @return bool
	 */
	public function has_property( $property ) {
		return array_key_exists( $property, $this->properties );
	}

	/**
	 * @param $property
	 *
	 * @return bool
	 */
	public function __isset( $property ) {
		return isset($this->properties[$property]);
	}

	/**
	 * @param $property
	 *
	 * @return mixed
	 * @throws \OutOfBoundsException
	 */
	public function __get( $property ) {
		if ( $this->has_property( $property ) ) {
			return $this->properties[$property];
		}
		throw new \OutOfBoundsException("$property is not a property of class");
	}

	/**
	 * @return array
	 */
	public function to_array() {
		return $this->properties;
	}
}
