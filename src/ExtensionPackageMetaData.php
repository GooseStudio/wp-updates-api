<?php

namespace GooseStudio\WpUpdatesAPI;

/**
 * Class ExtensionPackageMetaData
 * @package GooseStudio\WpUpdatesAPI
 * @property string slug
 * @property string new_version
 * @property string url
 * @property string package
 * @property int checked_timestamp
 */
class ExtensionPackageMetaData
{
	/**
	 * @var array
	 */
	private $properties;

	/**
	 * ExtensionInformation constructor.
	 */
	public function __construct() {
		$this->properties = 			[
			'slug' => '',
			'new_version' => '',
			'url' => '',
			'package' => '',
			'checked_timestamp' => 0,
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

	/**
	 * @return array
	 */
	public function to_array() {
		return $this->properties;
	}
}
