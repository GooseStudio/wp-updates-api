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
	public function __construct( string $endpoint, array $options = null ) {
		$this->options = $options;
		$this->endpoint = $endpoint;
	}

	/**
	 * @param string $extension_name
	 *
	 * @return string
	 * @throws WpUpdatesAPIException
	 */
	public function get_latest_version(string $extension_name)
    {
	    $headers     = [ 'Accept' => 'application/json' ];
	    $query_array = [ 'extension_name' => $extension_name];
	    $query       = http_build_query( $query_array );
	    $response    = Requests::get( $this->endpoint . '/products/version/?' . $query, $headers, $this->options );
	    if ( $response->success ) {
		    return json_decode( $response->body, true );
	    }
	    throw new WpUpdatesAPIException( $response->body, $response->status_code );
    }

	/**
	 * @return string
	 */
	public function get_endpoint(): string {
		return $this->endpoint;
	}

	/**
	 * @param string $extension_name
	 * @param string $license_key
	 *
	 * @return bool
	 * @throws WpUpdatesAPIException
	 */
    public function is_valid(string $extension_name, string $license_key) : bool
    {
	    $response = $this->make_license_request( $extension_name, $license_key );
	    if ( $response->success ) {
		    $result = json_decode( $response->body, true );
		    return isset($result['status']) && $result['status'] === 'valid';
	    }
	    throw new WpUpdatesAPIException( $response->body, $response->status_code );
    }

	/**
	 * @param string $extension_name
	 * @param string $license_key
	 *
	 * @return array
	 * @throws WpUpdatesAPIException
	 */
    public function get_license_key_data(string $extension_name, string $license_key) : array
    {
	    $response = $this->make_license_request( $extension_name, $license_key );
	    if ( $response->success ) {
		    return json_decode( $response->body, true );
	    }
	    throw new WpUpdatesAPIException( $response->body, $response->status_code );
    }

    public function register_license_key(string $extension_name, string $license_key, string $url)
    {
	    $headers     = [ 'Accept' => 'application/json' ];
	    $query_array = [ 'extension_name' => $extension_name, 'license_key' => $license_key, 'site' => $url];
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
	private function make_license_request( string $extension_name, string $license_key ): \Requests_Response {
		$headers     = [ 'Accept' => 'application/json' ];
		$query_array = [ 'extension_name' => $extension_name, 'license_key' => $license_key ];
		$query       = http_build_query( $query_array );
		$response    = Requests::get( $this->endpoint . '/licenses/valid/?' . $query, $headers, $this->options );

		return $response;
	}
}
