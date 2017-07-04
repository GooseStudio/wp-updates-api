<?php
use ArtOfWP\WP\Testing\WP_Bootstrap;
$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array(
		'my-plugin/my-plugin.php'
	)
);
if ( file_exists( __DIR__ . '/config.php' ) ) {
	require_once __DIR__ . '/config.php';
}
$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir && !defined('WP_TESTS_DIR')) {
	$_tests_dir = '/tmp/wordpress/';
	define( 'WP_TESTS_DIR', $_tests_dir );
}

(new WP_Bootstrap(WP_TESTS_DIR, __DIR__ . '/wp-tests-config.php'))->run();
