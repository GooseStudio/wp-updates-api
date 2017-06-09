<?php

namespace spec\GooseStudio\WpUpdatesAPI;

use GooseStudio\WpUpdatesAPI\WpUpdatesAPI;
use PhpSpec\ObjectBehavior;

/**
 * Class WpUpdatesAPISpec
 * @package spec\GooseStudio\WpUpdatesAPI
 * @mixin WpUpdatesAPI
 */
class WpUpdatesAPISpec extends ObjectBehavior {
	public function let() {
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/' );
	}

	public function it_should_take_extension_name_and_license_key() {
		$this->get_endpoint()->shouldBe( 'https://example.com/wp_updates_api/v1/' );
	}

	public function it_should_retrieve_latest_version() {
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [ 'version' => '4.0.1' ] );
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->get_latest_version( 'plugin_name' )->shouldBe( [ 'version' => '4.0.1' ] );
	}

	public function it_should_retrieve_extension_information() {
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [] );
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->get_extension_meta_data( 'plugin_name' )->shouldBe( [] );
	}

	public function it_should_retrieve_extension_package_info() {
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [
			'slug' => 'plugin_name',
			'new_version' => '1.1',
			'url' => 'https://example.com/plugin/plugin_name',
			'package' => 'https://example.com/wp_updates_api/v1/files/plugin_name?license_key=license_key',
		] );
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->get_extension_package_meta_data( 'plugin_name', 'license_key' )->shouldBe(
			[
				'slug' => 'plugin_name',
				'new_version' => '1.1',
				'url' => 'https://example.com/plugin/plugin_name',
				'package' => 'https://example.com/wp_updates_api/v1/files/plugin_name?license_key=license_key',
			]
		);
	}
}
