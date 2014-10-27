<?php

namespace Guide42\Choclo\Exception;

class ConflictException extends \LogicException {
    private $conflicts;

    public function __construct($message=null, $code=null, $previous=null,
        array $conflicts
    ) {
        $this->conflicts = $conflicts;

        parent::__construct($message, $code, $previous);
    }

    public function getConflicts() {
        return $this->conflicts;
    }
}