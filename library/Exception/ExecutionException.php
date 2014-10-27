<?php

namespace Guide42\Choclo\Exception;

class ExecutionException extends \RuntimeException {
    private $key;
    private $callable;

    public function __construct($message=null, $code=null, $previous=null,
        $key, callable $callable
    ) {
        $this->key = $key;
        $this->callable = $callable;

        parent::__construct($message, $code, $previous);
    }

    public function getKey() {
        return $this->key;
    }

    public function getCallable() {
        return $this->callable;
    }
}