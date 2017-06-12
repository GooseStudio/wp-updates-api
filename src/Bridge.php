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
	 * @var null
	 */
	private $options;
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
		$this->type = self::PLUGIN === $type ? self::PLUGIN : self::THEME;
		$this->type = $type;
		$this->file_name = $file_name;
		$this->extension_name = $extension_name;
		$this->license_key = $license_key;
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
		$package_data = $this->wp_updates_api->get_extension_package_meta_data($this->extension_name, $this->license_key);
		$package_data['checked_timestamp'] = time();
		$updates->response[$this->file_name] = $package_data;
		return $updates;
	}
}
