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
	private $file_name;

	/**
	 * Bridge constructor.
	 *
	 * @param string $type plugin or theme
	 * @param string $file_name
	 * @param string $extension_name
	 * @param string $license_key
	 * @param WpUpdatesAPI $wp_updates_api
	 */
	public function __construct( $type, $file_name, $extension_name, $license_key, WpUpdatesAPI $wp_updates_api ) {
		$this->type           = self::PLUGIN === $type ? self::PLUGIN : self::THEME;
		$this->file_name      = $file_name;
		$this->extension_name = $extension_name;
		$this->license_key    = $license_key;
		$this->wp_updates_api = $wp_updates_api;
	}

	public function build() {
		add_filter( 'site_transient_' . $this->type, array( $this, 'connect_update' ) );
	}

	/**
	 * @param $updates
	 *
	 * @return mixed
	 */
	public function connect_update( $updates ) {
	    try {
		$package_data = $this->wp_updates_api->get_extension_package_meta_data( $this->extension_name, $this->license_key );
		if ( version_compare( $this->get_local_plugin_version(), $package_data['new_version'], '<' ) ) {
			$package_data['checked_timestamp']     = time();
			$updates->response[ $this->file_name ] = $package_data;
		}
        } catch (WpUpdatesAPIException $exception) {
	        if (defined('WP_DEBUG') && WP_DEBUG) {
	            error_log("Update check failed for $this->extension_name with license $this->license_key with message " . $exception->getMessage());
            }
        }
		return $updates;
	}

	public function get_local_plugin_version() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data( plugin_dir_path( $this->file_name ), false, false );

		return $plugin_data['Version'];
	}
}
