<?php

namespace Worthwelle\Alphonic\Exception;

class AlphabetNotFoundException extends \Exception {
    public function __constructor() {
        parent::__construct('File not found');
    }
}

class InvalidAlphabetException extends \Exception {
    public function __constructor() {
        parent::__construct('Invalid alphabet');
    }
}
