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
			'name'              => 'Plugin Name',
			'slug'              => 'plugin-name',
			'homepage'          => 'https://goose.studio/plugins/content-tabs',
			'short_description' => '',
			'version'           => '2.0',
			'tested'            => '4.8',
			'author'            => '<a href="https://goose.studio">Goose Studio</a>,<a href="https://goose.studio">Goose Studio2</a>',
			'requires'          => '4.4',
			'rating'            => 90,
			'num_ratings'       => 5000,
			'downloaded'        => 500,
			'active_installs'   => 500,
			'banners'           => [
				'low'  => 'https://example.com/772x250.jpg',
				'high' => 'https://example.com/1544x500.jpg'
			],
			'last_updated'      => date( 'Y-m-d' ),
			'added'             => date( 'Y-m-d' ),
			'tags'              => [ 'black', 'test' ],
			'compatibility'     => '4.8',
			'donate_link' => 'https://goose.studio/plugins/content-tabs',
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
		if (array_key_exists($property, $this->properties)) {
			if (gettype($value) === gettype($this->properties[$property])) {
				$this->properties[$property] = $value;
			} else {
				throw new \UnexpectedValueException("$property value as wrong type.");
			}
		} else {
			throw new \OutOfBoundsException("$property is not a property of class");
		}
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
		if (array_key_exists($property, $this->properties)) {
			return $this->properties[$property];
		}
		throw new \OutOfBoundsException("$property is not a property of class");
	}
}
