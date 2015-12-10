<?php

namespace Guide42\Choclo;

use Guide42\Choclo\Exception\ExecutionException;

class Configurator implements ConfiguratorInterface
{
    public $path = '/';

    /**
     * @var Guide42\Choclo\ActionState
     */
    private $actions;

    public function __construct() {
        $this->actions = new ActionState();
        $this->rollback();
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