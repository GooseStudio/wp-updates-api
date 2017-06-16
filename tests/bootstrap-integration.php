<?php
use ArtOfWP\WP\Testing\WP_Bootstrap;
$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array(
		'my-plugin/my-plugin.php'
	)
);
include __DIR__ . '/config.php';
(new WP_Bootstrap(WP_TESTS_DIR, __DIR__ . '/wp-tests-config.php'))->run();
