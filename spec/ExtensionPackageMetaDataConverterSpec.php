<?php

namespace spec\GooseStudio\WpUpdatesAPI;

use GooseStudio\WpUpdatesAPI\ExtensionPackageMetaData;
use GooseStudio\WpUpdatesAPI\ExtensionPackageMetaDataConverter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExtensionPackageMetaDataConverterSpec extends ObjectBehavior
{
	public function it_should_convert_from_json_to_extension_information() {
		$json_data = file_get_contents(__DIR__ . '/extension-meta-data.json');
		$data = $this->convert_from_json($json_data);
		$data->slug->shouldEqual('plugin-name');
	}
	public function it_should_convert_from_extension_information_to_object() {
		$meta_data = new ExtensionPackageMetaData();
		$meta_data->slug = 'plugin-name';
		$meta_data->package = 'https://example.com?file';
		$meta_data->url = 'https://example.com';
		$meta_data->url = 'https://example.com';
		$data = $this->convert_to_object($meta_data);
		$data->shouldBeAnInstanceOf(\stdClass::class);
		$data->slug->shouldEqual('plugin-name');
	}
}
