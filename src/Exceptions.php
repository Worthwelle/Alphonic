<?php

/**
 * This file is part of the Alphonic package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Worthwelle\Alphonic\Exception;

/**
 * Exception that is raised when a queried alphabet is not loaded.
 */
class AlphabetNotFoundException extends \Exception {
    public function __construct($message = '') {
        parent::__construct($message);
    }
}

/**
 * Exception that is raised when an alphabet being loaded is invalid due to JSON or schema issues.
 */
class InvalidAlphabetException extends \Exception {
    public function __construct($message = '') {
        parent::__construct($message);
    }
}

/**
 * Exception that is raised when a locale requested isn't found in the alphabet.
 */
class LocaleNotFoundException extends \Exception {
    public function __construct($message = '') {
        parent::__construct($message);
    }
}

/**
 * Exception that is raised when a locale provided isn't in a proper format.
 */
class InvalidLocaleException extends \Exception {
    public function __construct($message = '') {
        parent::__construct($message);
    }
}
