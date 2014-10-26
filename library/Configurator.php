<?php

namespace Guide42\Choclo;

use Guide42\Suda\Registry;

class Configurator implements ConfiguratorInterface
{
    /**
     * @var \Guide42\Suda\RegistryInterface
     */
    private $registry;

    public function __construct(Registry $registry=null) {
        if ($registry === null) {
            $registry = new Registry();
        }

        $this->registry = $registry;
        $this->rollback();
    }

    public function getRegistry() {
        return $this->registry;
    }

    public function register($key, callable $configure, $phase=self::PHASE_DEFAULT) {
        // TODO
    }

    public function rollback() {
        // TODO
    }

    public function commit() {
        // TODO
    }
}