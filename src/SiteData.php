<?php
namespace GooseStudio\WpUpdatesAPI;


class SiteData {
	public function get_data() {
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
			'url' => $wp_install,
		);
	}
}