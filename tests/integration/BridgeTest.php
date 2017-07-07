<?php
use GooseStudio\WpUpdatesAPI\Bridge;
use GooseStudio\WpUpdatesAPI\WpUpdatesAPI;
use phpmock\mockery\PHPMockery as f;
use spec\GooseStudio\WpUpdatesAPI\MockTransport;
/**
 * Class BridgeTest
 */
class BridgeTest extends \ArtOfWP\WP\Testing\WP_UnitTestCase {
	/**
	 * @test
	 **/
	public function it_should_retrieve_update_notification_if_new_version() {
		f::mock('GooseStudio\WpUpdatesAPI', 'get_plugin_data')->withAnyArgs()->andReturn(
			array(
				'Name' => 'Plugin Name',
				'PluginURI' => 'Plugin URI',
				'Version' => '1.0',
				'Description' => 'Description',
				'Author' => 'Author',
				'AuthorURI' => 'Author URI',
				'TextDomain' => 'Text Domain',
				'DomainPath' => 'Domain Path',
				'Network' => 'Network',
			)
		);
		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [
			'slug'        => 'plugin_name',
			'new_version' => '1.1',
			'url'         => 'https://example.com/plugin/plugin_name',
			'package'     => 'https://example.com/wp_updates_api/v1/files/plugin_name?license_key=license_key',
		] );
		$api             = new WpUpdatesAPI( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$bridge = new Bridge('update_plugins', 'my-plugin/my-plugin.php', 'my-plugin','', $api);
		$bridge->build();
		wp_update_plugins();
		$update_plugins = get_site_transient('update_plugins');
		self::assertArrayHasKey('my-plugin/my-plugin.php', $update_plugins->response);
	}

	/**
	 * @test
	 **/
	public function it_should_not_get_update_if_no_version() {
		f::mock('GooseStudio\WpUpdatesAPI', 'get_plugin_data')->withAnyArgs()->andReturn(
			array(
				'Name' => 'Plugin Name',
				'PluginURI' => 'Plugin URI',
				'Version' => '1.0',
				'Description' => 'Description',
				'Author' => 'Author',
				'AuthorURI' => 'Author URI',
				'TextDomain' => 'Text Domain',
				'DomainPath' => 'Domain Path',
				'Network' => 'Network',
			)
		);

		$transport       = new MockTransport();
		$transport->code = '200';
		$transport->body = json_encode( [
			'slug'        => 'plugin_name',
			'new_version' => '1.0',
			'url'         => 'https://example.com/plugin/plugin_name',
			'package'     => 'https://example.com/wp_updates_api/v1/files/plugin_name?license_key=license_key',
		] );
		$api             = new WpUpdatesAPI( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
		$bridge = new Bridge('update_plugins', 'my-plugin/my-plugin.php', 'my-plugin','', $api);
		$bridge->build();
		wp_update_plugins();
		$update_plugins = get_site_transient('update_plugins');
		self::assertArrayNotHasKey('my-plugin/my-plugin.php', $update_plugins->response);
	}

    /**
     * @test
     */
    public function it_should_catch_exceptions()
    {
        f::mock('GooseStudio\WpUpdatesAPI', 'get_plugin_data')->withAnyArgs()->andReturn(
            array(
                'Name' => 'Plugin Name',
                'PluginURI' => 'Plugin URI',
                'Version' => '1.0',
                'Description' => 'Description',
                'Author' => 'Author',
                'AuthorURI' => 'Author URI',
                'TextDomain' => 'Text Domain',
                'DomainPath' => 'Domain Path',
                'Network' => 'Network',
            )
        );

        $transport       = new MockTransport();
        $transport->code = '500';
        $transport->body = 'Server Error';
        $api             = new WpUpdatesAPI( 'https://example.com/wp_updates_api/v1/', [ 'transport' => $transport ] );
        $bridge = new Bridge('update_plugins', 'my-plugin/my-plugin.php', 'my-plugin','', $api);
        $bridge->build();
        wp_update_plugins();
        $update_plugins = get_site_transient('update_plugins');
        self::assertArrayNotHasKey('my-plugin/my-plugin.php', $update_plugins->response);
	}

	/**
	 * @test
	 **/
	public function it_should_display_plugin_information() {
	}
}
