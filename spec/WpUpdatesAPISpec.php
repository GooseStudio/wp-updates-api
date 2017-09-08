<?php

namespace spec\GooseStudio\WpUpdatesAPI;

use GooseStudio\WpUpdatesAPI\WpUpdatesAPI;
use phpmock\mockery\PHPMockery;
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
		PHPMockery::mock( $this->get_ns( WpUpdatesAPI::class ), 'home_url' )->andReturn( '' );
		PHPMockery::mock( $this->get_ns( WpUpdatesAPI::class ), 'get_site_option' )->andReturn( '' );
		PHPMockery::mock( $this->get_ns( WpUpdatesAPI::class ), 'apply_filters' )->andReturn( '' );
		PHPMockery::mock( $this->get_ns( WpUpdatesAPI::class ), 'get_locale' )->andReturn( '' );
		PHPMockery::mock( $this->get_ns( WpUpdatesAPI::class ), 'is_multisite' )->andReturn( false );
		PHPMockery::mock( $this->get_ns( WpUpdatesAPI::class ), 'count_users' )->andReturn( 1 );
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [ 'version' => '4.0.1' ] );
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->get_latest_version( 'plugin_name' )->shouldBe( [ 'version' => '4.0.1' ] );
	}

	public function it_should_retrieve_extension_information() {
		$sections = array(
			'description'  => '<p>test</p>',
			'installation' => '<p>test</p>',
			'faq'          => '<p>test</p>',
			'screenshots'  => '<p>test</p>',
			'changelog'    => '<p>test</p>',
//			'reviews'      => ,
			'other_notes'  => '<p>test</p>'
		);
		$package = [
			'sections' => $sections,
			'name' => 'Plugin Name',
			'slug' => 'plugin-name',
			'homepage' => 'https://example.com/plugin/plugin-name',
		];
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( $package );
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->get_extension_meta_data( 'plugin_name' )->name->shouldBe('Plugin Name');
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
		$this->get_extension_package_meta_data( 'plugin_name', 'license_key' )->slug->shouldBe('plugin_name');
	}

	private function get_ns( $class ) {
		return substr( $class, 0, strrpos( $class, '\\' ) );
	}
}
