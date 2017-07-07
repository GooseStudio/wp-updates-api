<?php

namespace spec\GooseStudio\WpUpdatesAPI;

use GooseStudio\WpUpdatesAPI\ExtensionInformationConverter;
use GooseStudio\WpUpdatesAPI\ExtensionInformation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExtensionInformationConverterSpec extends ObjectBehavior
{
	public function it_should_convert_from_json_to_extension_information() {
		$json_data = file_get_contents(__DIR__ . '/extension-data.json');
		$data = $this->convert_from_json($json_data);
		$data->name->shouldEqual('Plugin Name');
		$data->slug->shouldEqual('plugin-name');
	}
}
