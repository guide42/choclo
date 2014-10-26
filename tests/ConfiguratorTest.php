<?php

namespace Guide42\ChocloTest;

use Guide42\Choclo\Configurator;

class ConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $config = new Configurator();

        $this->assertInstanceOf("Guide42\\Suda\\RegistryInterface",
            $config->getRegistry()
        );
    }
}