<?php

namespace Guide42\Choclo;

use Guide42\Suda\Registry;

class Configurator implements ConfiguratorInterface
{
    public $path = '/';

    /**
     * @var \Guide42\Suda\RegistryInterface
     */
    private $registry;

    /**
     * @var Guide42\Choclo\ActionState
     */
    private $actions;

    public function __construct(Registry $registry=null) {
        if ($registry === null) {
            $registry = new Registry();
        }

        $this->registry = $registry;
        $this->actions = new ActionState();

        $this->rollback();
    }

    public function getRegistry() {
        return $this->registry;
    }

    public function register($key, callable $configure, $phase=self::PHASE_DEFAULT) {
        $this->actions->push($phase, $key, $configure, $this->path);
    }

    public function rollback() {
        $this->actions->reset();
    }

    public function commit() {
        foreach ($this->actions->resolve() as $action) {
            list($key, $fn) = $action;

            try {
                call_user_func($fn);
            } catch (\Exception $e) {
                $msg = 'An error occurred during execution'
                     . ' of a configuration action';

                throw new ExecutionException($msg, null, $e, $key, $fn);
            }
        }

        $this->rollback();
    }
}