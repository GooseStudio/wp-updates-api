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
		$data_array  = json_decode($json, true);
		$properties  = array_keys( $information->to_array() );
		foreach ( $properties as $property ) {
			if ( isset( $data_array[ $property ] ) ) {
				$information->$property = $data_array[ $property ];
			}
		}

		if ( isset( $data_array['readme'] ) ) {
			/** @var array $data_array */
			foreach ( (array) $data_array['readme'] as $property => $value ) {
				if ( $information->has_property( $property ) ) {
					$information->$property = $value;
				}
			}
		}
		$convert   = array(
			'description'  => 'Description',
			'installation' => 'Installation',
			'faq'          => 'Frequently Asked Questions',
			'screenshots'  => 'Screensots',
			'changelog'    => 'Changelog',
			'reviews'      => 'Reviews',
			'other_notes'  => 'Other notes'
		);
		$convert   = array_flip( $convert );
		$formatted = [];
		foreach ( $information->sections as $key => $section ) {
			if ( isset( $convert[ $key ] ) ) {
				$formatted[ $convert[ $key ] ] = $section;
			}
		}
		$information->sections = $formatted;
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
