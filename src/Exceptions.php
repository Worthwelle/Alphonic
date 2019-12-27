<?php

namespace Worthwelle\Alphonic\Exception;

/**
 * Exception that is raised when a queried alphabet is not loaded.
 */
class AlphabetNotFoundException extends \Exception {
    public function __construct($message = '') {
        if ($message != '') {
            $message = ": $message";
        }
        parent::__construct('Alphabet not found' . $message);
    }
}

/**
 * Exception that is raised when an alphabet being loaded is invalid due to JSON or schema issues.
 */
class InvalidAlphabetException extends \Exception {
    public function __construct($message = '') {
        if ($message != '') {
            $message = ": $message";
        }
        parent::__construct('Invalid alphabet' . $message);
    }
}
