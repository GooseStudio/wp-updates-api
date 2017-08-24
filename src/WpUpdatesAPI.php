<?php

namespace GooseStudio\WpUpdatesAPI;

use Requests;

class WpUpdatesAPI {
	/**
	 * @var array
	 */
	private $options;
	/**
	 * @var string
	 */
	private $endpoint;

	/**
	 * WpUpdatesAPI constructor.
	 *
	 * @param string $endpoint
	 * @param array $options
	 */
	public function __construct( $endpoint, $options = null ) {
		$this->options  = $options;
		$this->endpoint = $endpoint;
	}

	/**
	 * Retrieves latest version number for a plugin or a theme.
	 *
	 * @param string $extension_name
	 *
	 * @return string
	 * @throws WpUpdatesAPIException
	 */
	public function get_latest_version( $extension_name ) {
		$headers  = [ 'Accept' => 'application/json' ];
		$response = Requests::get( $this->endpoint . '/extensions/' . urlencode( $extension_name ) . '/version/', $headers, $this->options );
		if ( $response->success ) {
			return json_decode( $response->body, true );
		}
		throw new WpUpdatesAPIException( $response->body, $response->status_code );
	}

	/**
	 * @return string
	 */
	public function get_endpoint() {
		return $this->endpoint;
	}

	/**
	 * @param string $extension_name
	 *
	 * @return ExtensionInformation
	 * @throws WpUpdatesAPIException
	 */
	public function get_extension_meta_data( $extension_name ) {
		$headers  = [ 'Accept' => 'application/json' ];
		$response = Requests::get( $this->endpoint . '/extensions/' . urlencode( $extension_name ), $headers, $this->options );
		if ( $response->success ) {
			return (new ExtensionInformationConverter())->convert_from_json($response->body);
		}
		throw new WpUpdatesAPIException( $response->body, $response->status_code );
	}

	/**
	 * @param string $extension_name
	 * @param string $license_key
	 *
	 * @return ExtensionPackageMetaData
	 * @throws WpUpdatesAPIException
	 */
	public function get_extension_package_meta_data( $extension_name, $license_key ) {
		$headers     = [ 'Accept' => 'application/json' ];
		$query_array = [ 'extension_name' => $extension_name, 'license_key' => $license_key ];
		$query       = http_build_query( $query_array );
		$response    = Requests::get( $this->endpoint . '/extensions/' . urlencode( $extension_name ) . '/package/' . '?' . $query, $headers, $this->options );
		if ( $response->success ) {
			return (new ExtensionPackageMetaDataConverter())->convert_from_json($response->body);
		}
		throw new WpUpdatesAPIException( $response->body, $response->status_code );
	}
}
