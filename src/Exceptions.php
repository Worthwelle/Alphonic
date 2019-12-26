<?php

namespace Worthwelle\Alphonic\Exception;

/**
 * Exception that is raised when a queried alphabet is not loaded.
 */
class AlphabetNotFoundException extends \Exception {
    public function __construct() {
        parent::__construct('Alphabet not found');
    }
}

/**
 * Exception that is raised when an alphabet being loaded is invalid due to JSON or schema issues.
 */
class InvalidAlphabetException extends \Exception {
    public function __construct() {
        parent::__construct('Invalid alphabet');
    }
}
