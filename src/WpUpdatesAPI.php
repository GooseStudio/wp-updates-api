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
		$query       = http_build_query( $this->query_array($extension_name, '' ) );
		$response = Requests::get( $this->endpoint . '/extensions/' . urlencode( $extension_name ) . '/version/?' . $query, $headers, $this->options );
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
		$query       = http_build_query( $this->query_array($extension_name, '' ) );
		$response = Requests::get( $this->endpoint . '/extensions/' . urlencode( $extension_name ) . '/?' . $query, $headers, $this->options );
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
		$query       = http_build_query( $this->query_array($extension_name, $license_key ) );
		$response    = Requests::get( $this->endpoint . '/extensions/' . urlencode( $extension_name ) . '/package/' . '?' . $query, $headers, $this->options );
		if ( $response->success ) {
			return (new ExtensionPackageMetaDataConverter())->convert_from_json($response->body);
		}
		throw new WpUpdatesAPIException( $response->body, $response->status_code );
	}

	/**
	 * @param $extension_name
	 * @param $license_key
	 *
	 * @return array
	 */
	public function query_array( $extension_name, $license_key ) {
		global $wp_version, $wpdb;
		include ABSPATH . WPINC . '/version.php';
		$php_version = PHP_VERSION;

		/**
		 * Filters the locale requested for WordPress core translations.
		 *
		 * @since 2.8.0
		 *
		 * @param string $locale Current locale.
		 */
		$locale = apply_filters( 'core_version_check_locale', get_locale() );

		// Update last_checked for current to prevent multiple blocking requests if request hangs

		if ( method_exists( $wpdb, 'db_version' ) )
			$mysql_version = preg_replace('/[^0-9.].*/', '', $wpdb->db_version());
		else
			$mysql_version = 'N/A';

		if ( is_multisite() ) {
			$user_count = get_user_count();
			$num_blogs = get_blog_count();
			$wp_install = network_site_url();
			$multisite_enabled = 1;
		} else {
			$user_count = count_users();
			$user_count = $user_count['total_users'];
			$multisite_enabled = 0;
			$num_blogs = 1;
			$wp_install = home_url( '/' );
		}

		return array(
			'version'            => $wp_version,
			'php'                => $php_version,
			'locale'             => $locale,
			'mysql'              => $mysql_version,
			'local_package'      => isset( $wp_local_package ) ? $wp_local_package : '',
			'blogs'              => $num_blogs,
			'users'              => $user_count,
			'multisite_enabled'  => $multisite_enabled,
			'initial_db_version' => get_site_option( 'initial_db_version' ),
			'extension_name'     => $extension_name,
			'license_key'        => $license_key,
			'url' => $wp_install,
		);
	}
}
