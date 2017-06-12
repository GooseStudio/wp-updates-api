<?php

namespace spec\GooseStudio\WpUpdatesAPI;

use GooseStudio\WpUpdatesAPI\Bridge;
use GooseStudio\WpUpdatesAPI\WpUpdatesAPI;
use phpmock\prophecy\PHPProphet;
use PhpSpec\ObjectBehavior;

/**
 * Class BridgeSpec
 * @package spec\GooseStudio\WpUpdatesAPI
 * @mixed Bridge
 */
class BridgeSpec extends ObjectBehavior {
	public function it_should_hook_into_updates_plugins( WpUpdatesAPI $api ) {
		$this->beConstructedWith( Bridge::PLUGIN, 'test-plugin/test-plugin.php', 'test-plugin', '', $api );
		$prophet  = new PHPProphet();
		$prophecy = $prophet->prophesize( $this->get_ns( Bridge::class ) );
		$prophecy->add_filter( 'site_transient_update_plugins', array(
			$this->getWrappedObject(),
			'connect_update'
		) )->shouldBeCalled();
		$prophecy->reveal();
		$this->build();
		$prophet->checkPredictions();
	}

	public function it_should_call_remote() {
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [
			'slug'        => 'plugin_name',
			'new_version' => '1.1',
			'url'         => 'https://example.com/plugin/plugin_name',
			'package'     => 'https://example.com/wp_updates_api/v1/files/plugin_name?license_key=license_key',
		] );
		$api             = new WpUpdatesAPI( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->beConstructedWith( Bridge::PLUGIN, 'test-plugin/test-plugin.php', 'test-plugin', '', $api );
		$updates                  = new \stdClass();
		$updates->response        = [];
		$updates_result           = new \stdClass();
		$updates_result->response = [
			'test-plugin/test-plugin.php' =>
				(object) [
					'slug'        => 'plugin_name',
					'new_version' => '1.1',
					'url'         => 'https://example.com/plugin/plugin_name',
					'package'     => 'https://example.com/wp_updates_api/v1/files/plugin_name?license_key=license_key',
				]
		];
		$result                   = $this->connect_update( $updates )->shouldHavePropertyValue( 'test-plugin/test-plugin.php' );
		assert( $result->response = $updates_result->response );
	}

	function get_ns( $class ) {
		return substr( $class, 0, strrpos( $class, '\\' ) );
	}

	public function getMatchers() {
		return [
			'havePropertyValue' => function ( $subject, $key ) {
				return array_key_exists( $key, $subject->response );
			}
		];
	}
}
