<?php

namespace spec\GooseStudio\WpUpdatesAPI;

use GooseStudio\WpUpdatesAPI\WpLicenseAPI;
use PhpSpec\ObjectBehavior;

/**
 * Class WpLicenseAPISpec
 * @package spec\GooseStudio\WpUpdatesAPI
 * @mixed WpLicenseAPI
 */
class WpLicenseAPISpec extends ObjectBehavior
{

	public function it_should_verify_license_key_is_valid() {
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [ 'status' => 'valid' ] );
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->is_valid( 'plugin_name', 'abcdef' )->shouldBe( true );
	}

	public function it_should_verify_license_key_is_invalid() {
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [ 'status' => 'invalid' ] );
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->is_valid( 'plugin_name', 'abcdef' )->shouldBe( false );
	}

	public function it_should_verify_license_key_is_invalid_expired() {
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [ 'status' => 'expired', 'expiration_date' => '2017-08-18' ] );
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->get_license_key_data( 'plugin_name', 'abcdef' )->shouldBe( [
			'status'          => 'expired',
			'expiration_date' => '2017-08-18'
		] );
	}

	public function it_should_return_license_key_data() {
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [
			'status'             => 'expired',
			'expiration_date'    => '2017-08-18',
			'license_key_holder' => 'Widgets Inc',
			'licensed_sites_limit'     => 3,
			'licensed_sites_count'           => 2,
		] );
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->get_license_key_data( 'plugin_name', 'abcdef' )->shouldBe( [
				'status'             => 'expired',
				'expiration_date'    => '2017-08-18',
				'license_key_holder' => 'Widgets Inc',
				'licensed_sites_limit'     => 3,
				'licensed_sites_count'           => 2,
			]
		);
	}

	public function it_should_register_site_with_key() {
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [
			'status'             => 'registered',
			'expiration_date'    => '2017-08-18',
			'license_key_holder' => 'Widgets Inc',
			'site' => 'https://example.org/',
			'licensed_sites_limit'           => 3,
			'licensed_sites_count'           => 2,
			'registered_site' => 'https://example.org/'
		] );
		$this->beConstructedWith( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$this->register_license_key( 'plugin_name', 'abcdef', 'https://example.org/' )->shouldBe( [
				'status'             => 'registered',
				'expiration_date'    => '2017-08-18',
				'license_key_holder' => 'Widgets Inc',
				'site' => 'https://example.org/',
				'licensed_sites_limit'           => 3,
				'licensed_sites_count'           => 2,
				'registered_site' => 'https://example.org/'
			]
		);
	}
}
