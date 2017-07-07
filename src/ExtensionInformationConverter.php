<?php

namespace GooseStudio\WpUpdatesAPI;

/**
 * Class ExtensionInformationConverter
 * @package GooseStudio\WpUpdatesAPI
 */
class ExtensionInformationConverter {

	/**
	 * @param string $json
	 *
	 * @return ExtensionInformation
	 */
	public function convert_from_json( $json ) {
		$information = new ExtensionInformation();
		$data_array = json_decode($json, true);
		/** @var array $data_array */
		foreach ( $data_array as $property => $value ) {
			if ( isset($information->$property)  ) {
				$information->$property = $value;
			}
		}
		return $information;
	}
	/**
	 * @param ExtensionInformation $extension_information
	 *
	 * @return \stdClass
	 */
	public function convert_to_object($extension_information)
	{
		return (object) $extension_information->to_array();
	}
}
