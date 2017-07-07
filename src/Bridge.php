<?php

namespace GooseStudio\WpUpdatesAPI;

class Bridge {
	const PLUGIN = 'update_plugins';
	const THEME = 'update_themes';
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var string
	 */
	private $license_key;
	/**
	 * @var WpUpdatesAPI
	 */
	private $wp_updates_api;
	/**
	 * @var string
	 */
	private $file;
	/**
	 * @var bool
	 */
	private $override_extension_information = false;

	/**
	 * Bridge constructor.
	 *
	 * @param string $type plugin or theme
	 * @param string $file the plugin or themes __FILE__
	 * @param string $extension_name
	 * @param string $license_key
	 * @param WpUpdatesAPI $wp_updates_api
	 */
	public function __construct( $type, $file, $extension_name, $license_key, WpUpdatesAPI $wp_updates_api ) {
		$this->type           = self::PLUGIN === $type ? self::PLUGIN : self::THEME;
		$this->file           = $file;
		$this->extension_name = $extension_name;
		$this->license_key    = $license_key;
		$this->wp_updates_api = $wp_updates_api;
	}

	public function build() {
		add_filter( 'site_transient_' . $this->type, array( $this, 'connect_update' ) );
		if ( $this->override_extension_information ) {
			add_action( 'install_plugins_pre_plugin-information', array( $this, 'render_extension_information' ) );
		} else {
			add_filter( 'plugins_api', array( $this, 'extension_information' ), 10, 3 );
		}
		if ( self::PLUGIN === $this->type ) {
			add_filter( 'http_request_args', array($this, 'disable_wp_org_plugin_checks'), 10, 2 );
		}
		if ( self::THEME === $this->type ) {
			add_filter( 'http_request_args', array($this, 'disable_wp_org_theme_checks'), 10, 2 );
		}
	}

	public function disable_wp_org_theme_checks ( $response, $url ) {
		if ( 0 === strpos( $url, 'https://api.wordpress.org/themes/update-check' ) ) {
			$themes = json_decode( $response['body']['themes'] );
			unset( $themes->themes->{get_option( 'template' )} );
			unset( $themes->themes->{get_option( 'stylesheet' )} );
			$response['body']['themes'] = json_encode( $themes );
		}

		return $response;
	}
	public function disable_wp_org_plugin_checks ( $response, $url ) {
		if ( 0 === strpos( $url, 'https://api.wordpress.org/plugins/update-check' ) ) {
			$basename = plugin_basename( $this->file );
			$plugins  = json_decode( $response['body']['plugins'] );
			unset( $plugins->plugins->$basename );
			unset( $plugins->active[ array_search( $basename, $plugins->active ) ] );
			$response['body']['plugins'] = json_encode( $plugins );
		}

		return $response;
	}

	/**
	 * @param bool $state Set true to disable the default WordPress plugin/theme information page.
	 */
	public function override_extension_information( $state = true ) {
		$this->override_extension_information = $state;
	}

	/**
	 * @param $response
	 * @param $action
	 * @param $args
	 *
	 * @return \stdClass
	 */
	public function extension_information( $response, $action, $args ) {
		if ( 'plugin_information' === $action && dirname( plugin_basename( $this->file ) ) === $args->slug ) {
			$response                          = new \stdClass();
			$response->sections                = [];
			$response->name                    = $this->extension_name;
			$response->slug                    = 'content-tabs';
			$response->homepage                = 'https://goose.studio/plugins/content-tabs';
			$response->sections['description'] = '<p>testing</p>';
			$response->external                = true;

			return $response;
		}

		return $response;
	}

	/**
	 * @param $updates
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function connect_update( $updates ) {
		try {
			$package_data = $this->wp_updates_api->get_extension_package_meta_data( $this->extension_name, $this->license_key );
			if ( version_compare( $this->get_local_plugin_version(), $package_data->new_version, '<' ) ) {
				$package_data->checked_timestamp                     = time();
				$updates->response[ plugin_basename( $this->file ) ] = ( new ExtensionPackageMetaDataConverter() )->convert_to_object( $package_data );
			}
		} catch ( WpUpdatesAPIException $exception ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				throw new \Exception( "Update check failed for $this->extension_name with license $this->license_key with message " . $exception->getMessage() );
			}
		}

		return $updates;
	}

	public function get_local_plugin_version() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data( $this->file, false, false );

		return $plugin_data['Version'];
	}
}
