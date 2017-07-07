<?php

namespace spec\GooseStudio\WpUpdatesAPI;

use GooseStudio\WpUpdatesAPI\ExtensionPackageMetaData;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExtensionPackageMetaDataSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ExtensionPackageMetaData::class);
    }
}
