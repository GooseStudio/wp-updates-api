<?php

namespace GooseStudio\WpUpdatesAPI;

class ExtensionPackageMetaDataConverter
{
	/**
	 * @param string $json
	 *
	 * @return ExtensionPackageMetaData
	 */
	public function convert_from_json( $json ) {
		$information = new ExtensionPackageMetaData();
		if (is_null($json) || empty($json))
			return $information;

		$data_array  = json_decode( $json, true );
		if (is_null($data_array ) || empty($data_array ))
			return $information;

		/** @var array $data_array */
		foreach ( $data_array as $property => $value ) {
			if ( isset($information->$property ) ) {
				$information->$property = $value;
			}
		}
		return $information;
	}

	/**
	 * @param ExtensionPackageMetaData $extension_package_meta_data
	 *
	 * @return \stdClass
	 */
    public function convert_to_object($extension_package_meta_data)
    {
    	return (object) $extension_package_meta_data->to_array();
    }
}
