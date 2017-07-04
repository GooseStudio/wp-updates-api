<?php

namespace spec\GooseStudio\WpUpdatesAPI;

use GooseStudio\WpUpdatesAPI\Bridge;
use GooseStudio\WpUpdatesAPI\WpUpdatesAPI;
use phpmock\mockery\PHPMockery;
use phpmock\prophecy\PHPProphet;
use PhpSpec\ObjectBehavior;

/**
 * Class BridgeSpec
 * @package spec\GooseStudio\WpUpdatesAPI
 * @mixed Bridge
 */
class BridgeSpec extends ObjectBehavior {
	/**
	 * @var PHPProphet
	 */
	private $prophet;
	public function let() {
		$this->prophet  = new PHPProphet();
	}
	public function it_should_hook_into_updates_plugins( WpUpdatesAPI $api ) {
		$this->beConstructedWith( Bridge::PLUGIN, 'test-plugin/test-plugin.php', 'test-plugin', '', $api );
		PHPMockery::mock($this->get_ns( Bridge::class ),'add_filter')->twice();
/**		PHPMockery::mock($this->get_ns( Bridge::class ),'add_filter')->withArgs(array('site_transient_update_plugins', array(
			$this->getWrappedObject(),
			'connect_update'
		)))->once();

		$prophecy = $this->prophet->prophesize( $this->get_ns( Bridge::class ) );
		/** @noinspection PhpUndefinedMethodInspection */
/*		$prophecy->add_filter( 'site_transient_update_plugins', array(
			$this->getWrappedObject(),
			'connect_update'
		) )->shouldBeCalled();
		/** @noinspection PhpUndefinedMethodInspection */
/*		$prophecy->add_filter( 'plugins_api', array(
			$this->getWrappedObject(),
			'extension_information'
		), 10, 3 )->shouldBeCalled();*/

		//$prophecy->reveal();
		$this->build();
		//$this->prophet->checkPredictions();
	}

	public function it_should_disable_default_wp_plugin_information( WpUpdatesAPI $api ) {
		$this->beConstructedWith( Bridge::PLUGIN, 'test-plugin/test-plugin.php', 'test-plugin', '', $api );
		$this->override_extension_information(false);
		$this->prophet  = new PHPProphet();
		$prophecy = $this->prophet->prophesize( $this->get_ns( Bridge::class ) );
		/** @noinspection PhpUndefinedMethodInspection */
		$prophecy->add_action( 'install_plugins_pre_plugin-information', array(
			$this->getWrappedObject(),
			'render_extension_information'
		) )->shouldBeCalled();
		/** @noinspection PhpUndefinedMethodInspection */
		$prophecy->add_filter( 'site_transient_update_plugins', array(
			$this->getWrappedObject(),
			'connect_update'
		) )->shouldBeCalled();
		$prophecy->reveal();
		$this->override_extension_information(true);
		$this->build();
		$this->prophet->checkPredictions();
	}

	public function it_should_call_remote() {
		$this->prophet = new PHPProphet();
		$prophecy = $this->prophet->prophesize( $this->get_ns( Bridge::class ) );
		/** @noinspection PhpUndefinedMethodInspection */
		$prophecy->function_exists('get_plugin_data')->willReturn(true);
		/** @noinspection PhpUndefinedMethodInspection */
		$prophecy->plugin_basename('test-plugin/test-plugin.php')->willReturn('test-plugin/test-plugin.php');
		PHPProphet::define($this->get_ns( Bridge::class ), 'get_plugin_data');
		/** @noinspection PhpUndefinedMethodInspection */
		$prophecy->get_plugin_data('test-plugin/test-plugin.php', false, false)->willReturn(array(
			'Name' => 'Plugin Name',
			'PluginURI' => 'Plugin URI',
			'Version' => '1.0',
			'Description' => 'Description',
			'Author' => 'Author',
			'AuthorURI' => 'Author URI',
			'TextDomain' => 'Text Domain',
			'DomainPath' => 'Domain Path',
			'Network' => 'Network',
		));
		$prophecy->reveal();

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
		$this->override_extension_information(false);
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
		$result                   = $this->connect_update( $updates )->getWrappedObject();
		$this->connect_update( $updates )->shouldHavePropertyValue( 'test-plugin/test-plugin.php' );
		assert( is_object($result->response["test-plugin/test-plugin.php"]));
	}

	private function get_ns( $class ) {
		return substr( $class, 0, strrpos( $class, '\\' ) );
	}

	public function getMatchers() {
		return [
			'havePropertyValue' => function ( $subject, $key ) {
				return array_key_exists( $key, $subject->response );
			}
		];
	}

	public function letGo() {
		$this->prophet->checkPredictions();
	}
}
