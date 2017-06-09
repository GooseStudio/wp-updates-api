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
		$response = Requests::get( $this->endpoint . '/products/' . $extension_name . '/version/', $headers, $this->options );
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
	 * Checks if a license key is valid
	 *
	 * @param string $extension_name
	 * @param string $license_key
	 *
	 * @return bool
	 * @throws WpUpdatesAPIException
	 */
	public function is_valid( $extension_name, $license_key ) {
		$response = $this->make_license_request( $extension_name, $license_key );
		if ( $response->success ) {
			$result = json_decode( $response->body, true );

			return isset( $result['status'] ) && $result['status'] === 'valid';
		}
		throw new WpUpdatesAPIException( $response->body, $response->status_code );
	}

	/**
	 * Retrieves the data that is connected with plugin/theme and license key.
	 *
	 * @param string $extension_name
	 * @param string $license_key
	 *
	 * @return array
	 * @throws WpUpdatesAPIException
	 */
	public function get_license_key_data( $extension_name, $license_key ) {
		$response = $this->make_license_request( $extension_name, $license_key );
		if ( $response->success ) {
			return json_decode( $response->body, true );
		}
		throw new WpUpdatesAPIException( $response->body, $response->status_code );
	}

	/**
	 * Register license for the provided site url
	 *
	 * @param string $extension_name The plugin or theme that the license belongs to
	 * @param string $license_key The license key to use
	 * @param string $url The url to register the license to
	 *
	 * @return array
	 * @throws WpUpdatesAPIException
	 */
	public function register_license_key( $extension_name, $license_key, $url ) {
		$headers     = [ 'Accept' => 'application/json' ];
		$query_array = [ 'extension_name' => $extension_name, 'license_key' => $license_key, 'site' => $url ];
		$response    = Requests::post( $this->endpoint . '/licenses/', $headers, $query_array, $this->options );
		if ( $response->success ) {
			return json_decode( $response->body, true );
		}
		throw new WpUpdatesAPIException( $response->body, $response->status_code );
	}

	/**
	 * @param string $extension_name
	 * @param string $license_key
	 *
	 * @return \Requests_Response
	 */
	private function make_license_request( $extension_name, $license_key ) {
		$headers     = [ 'Accept' => 'application/json' ];
		$query_array = [ 'extension_name' => $extension_name, 'license_key' => $license_key ];
		$query       = http_build_query( $query_array );
		$response    = Requests::get( $this->endpoint . '/licenses/valid/?' . $query, $headers, $this->options );

		return $response;
	}

	/**
	 * @param string $extension_name
	 *
	 * @return array
	 * @throws WpUpdatesAPIException
	 */
	public function get_extension_meta_data( $extension_name ) {
		$headers  = [ 'Accept' => 'application/json' ];
		$response = Requests::get( $this->endpoint . '/products/' . urlencode( $extension_name ), $headers, $this->options );
		if ( $response->success ) {
			return json_decode( $response->body, true );
		}
		throw new WpUpdatesAPIException( $response->body, $response->status_code );
	}

	public function get_extension_package_meta_data( $extension_name, $license_key ) {
		$headers  = [ 'Accept' => 'application/json' ];
		$query_array = [ 'extension_name' => $extension_name, 'license_key' => $license_key ];
		$query       = http_build_query( $query_array );
		$response = Requests::get( $this->endpoint . '/products/package/' . urlencode( $extension_name ), $headers, $this->options );
		if ( $response->success ) {
			return json_decode( $response->body, true );
		}
		throw new WpUpdatesAPIException( $response->body, $response->status_code );
	}
}
