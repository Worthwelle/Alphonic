<?php

/**
 * This file is part of the Alphony package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Tests\TestCase;
use Worthwelle\Alphonic\Exception\AlphabetNotFoundException;
use Worthwelle\Alphonic\Exception\InvalidAlphabetException;

class ExceptionsTest extends TestCase {
    /**
     * Construct an AlphabetNotFoundException.
     *
     * @return void
     */
    public function testAlphabetNotFoundException() {
        $exception = new AlphabetNotFoundException();
        $this->assertInstanceOf(AlphabetNotFoundException::class, $exception);
        $this->assertSame('Alphabet not found', $exception->getMessage());
    }

    /**
     * Construct an InvalidAlphabetException.
     *
     * @return void
     */
    public function testInvalidAlphabetException() {
        $exception = new InvalidAlphabetException();
        $this->assertInstanceOf(InvalidAlphabetException::class, $exception);
        $this->assertSame('Invalid alphabet', $exception->getMessage());
    }
}
