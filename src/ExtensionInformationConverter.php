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
		$data_array = json_decode($json);
		/** @var array $data_array */
		foreach ( $data_array as $property => $value ) {
			if ( property_exists( $information, $property ) ) {
				$information->$property = $value;
			}
		}
		return $information;
	}
}
