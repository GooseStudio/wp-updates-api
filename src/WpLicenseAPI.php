<?php
namespace GooseStudio\WpUpdatesAPI;
use Requests;

class WpLicenseAPI
{
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
	 * Checks if a license key is valid
	 *
	 * @param array $extensions [$extension_name => $license_key]
	 *
	 * @return array
	 * @throws WpUpdatesAPIException
	 */
	public function all_is_valid( $extensions ) {
		$headers     = [ 'Accept' => 'application/json' ];
		$query_array = ['extensions' => $extensions];
		$query       = http_build_query( $query_array );
		$response    = Requests::get( $this->endpoint . '/licenses/?' . $query, $headers, $this->options );

		if ( $response->success ) {
			$result = json_decode( $response->body, true );
			$all = [];
			foreach ($extensions as $extension => $key) {
				$all[$extension] = isset( $result[$extension]['status'] ) && $result[$extension]['status'] === 'valid';
			}
			return $all;
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
		$response    = Requests::get( $this->endpoint . '/licenses/'.$extension_name.'/?' . $query, $headers, $this->options );

		return $response;
	}
}
