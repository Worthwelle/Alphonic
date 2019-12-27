<?php

/**
 * This file is part of the Alphony package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Tests\TestCase;
use Worthwelle\Alphonic\Alphabet;
use Worthwelle\Alphonic\Exception\InvalidAlphabetException;

class AlphabetTest extends TestCase {
    /**
     * Load a valid alphabet via the constructor.
     *
     * @return void
     */
    public function testLoadAlphabet() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->code, 'NATO');
    }

    /**
     * Load a valid alphabet via the from_json static method.
     *
     * @depends testLoadAlphabet
     *
     * @return void
     */
    public function testLoadAlphabetFromJSON() {
        $alpha = Alphabet::from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alpha->get_code(), 'NATO');
    }

    /**
     * Load a valid alphabet via the from_file static method.
     *
     * @depends testLoadAlphabetFromJSON
     *
     * @return void
     */
    public function testLoadAlphabetFromFile() {
        $alpha = Alphabet::from_file(__DIR__ . '/../../resources/test_alphabets/valid_nato.json');
        $this->assertEquals($alpha->get_code(), 'NATO');
    }

    /**
     * Load an invalid alphabet via the constructor.
     *
     * @return void
     */
    public function testLoadInvalidAlphabet() {
        $this->expectException(InvalidAlphabetException::class);
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/invalid_nato.json')));
    }

    /**
     * Load an invalid JSON file via the from_file static method.
     *
     * @depends testLoadAlphabet
     *
     * @return void
     */
    public function testLoadInvalidJSON() {
        $this->expectException(InvalidAlphabetException::class);
        $alpha = Alphabet::from_file(__DIR__ . '/../../resources/test_alphabets/invalid_json_nato.json');
    }

    /**
     * Add a symbol to the alphabet and verify it is used in converting.
     *
     * @return void
     */
    public function testAddSymbol() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $alpha->add_symbol(':', 'Colon');
        $this->assertEquals($alpha->phonetify('nato:'), 'November Alfa Tango Oscar Colon');
    }

    /**
     * Add a symbol to the alphabet and verify it does not overwrite an existing value.
     *
     * @return void
     */
    public function testAddSymbolWithoutOverwrite() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $alpha->add_symbol('A', 'Adam', false);
        $this->assertEquals($alpha->phonetify('nato'), 'November Alfa Tango Oscar');
    }

    /**
     * Add a symbol to the alphabet with a representation that is already assigned to a different symbol.
     *
     * @return void
     */
    public function testAddSymbolWithRepresentationConflict() {
        $this->expectException(InvalidAlphabetException::class);
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $alpha->add_symbol(')', 'Alfa');
    }

    /**
     * Add multiple symbols to the alphabet and verify they are used in converting, but don't overwrite existing values.
     *
     * @return void
     */
    public function testAddSymbols() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $alpha->add_symbols(array(':' => 'Colon', ';' => 'Semicolon'));
        $this->assertEquals($alpha->phonetify('nato:;'), 'November Alfa Tango Oscar Colon Semicolon');
    }

    /**
     * Add multiple symbols to the alphabet and verify they are used in converting.
     *
     * @return void
     */
    public function testAddSymbolsWithoutOverwrite() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $alpha->add_symbols(array('A' => 'Adam', ':' => 'Colon', ';' => 'Semicolon'), false);
        $this->assertEquals($alpha->phonetify('nato:;'), 'November Alfa Tango Oscar Colon Semicolon');
    }

    /**
     * Test converting a letter to its phonetic representation
     *
     * @depends testLoadAlphabet
     *
     * @return void
     */
    public function testGetSymbolRepresentation() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->get_symbol_represenation('A'), 'Alfa');
    }

    /**
     * Test converting a letter missing from the alphabet to its phonetic representation.
     *
     * @depends testLoadAlphabet
     *
     * @return void
     */
    public function testGetMissingSymbolRepresentation() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->get_symbol_represenation(':'), '');
    }

    /**
     * Test converting a letter missing from the alphabet to its phonetic representation.
     *
     * @depends testLoadAlphabet
     *
     * @return void
     */
    public function testGetMissingSymbolRepresentationReturnMissing() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->get_symbol_represenation(':', true), ':');
    }

    /**
     * Test converting a phonetic representation to its symbol.
     *
     * @depends testLoadAlphabet
     *
     * @return void
     */
    public function testGetSymbolFromRepresentation() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->get_symbol_from_represenation('Alfa', true), 'A');
    }

    /**
     * Test converting a phonetic representation missing from the alphabet to its symbol.
     *
     * @depends testLoadAlphabet
     *
     * @return void
     */
    public function testGetMissingSymbolFromRepresentation() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->get_symbol_from_represenation(':'), null);
    }

    /**
     * Test converting a phonetic representation missing from the alphabet to its symbol
     *
     * @depends testLoadAlphabet
     *
     * @return void
     */
    public function testGetMissingSymbolFromRepresentationWithReturnMissing() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->get_symbol_from_represenation(':', true), ':');
    }

    /**
     * Convert a case-insensitive string to phonetics.
     *
     * @return void
     */
    public function testPhonetifyStringCaseInsensitive() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->phonetify('nato'), 'November Alfa Tango Oscar');
    }

    /**
     * Convert a case-insensitive phonetic string to a string.
     *
     * @return void
     */
    public function testUnphonetifyStringCaseInsensitive() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->unphonetify('November Alfa Tango Oscar'), 'NATO');
    }

    /**
     * Convert a case-insensitive string with symbols not in the alphabet to phonetics.
     *
     * Expected to convert the string without the missing symbols.
     *
     * @return void
     */
    public function testPhonetifyStringWithMissingSymbols() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->phonetify('nato:'), 'November Alfa Tango Oscar');
    }

    /**
     * Convert a case-insensitive string with symbols not in the alphabet to phonetics, but leave the missing symbols untouched.
     *
     * Expected to convert the string with the missing symbols left untouched.
     *
     * @return void
     */
    public function testPhonetifyStringWithMissingSymbolsAndReturnMissing() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->phonetify('nato:', true), 'November Alfa Tango Oscar :');
    }

    /**
     * Convert a case-insensitive phonetic string with symbols not in the alphabet to a string.
     *
     * Expected to convert the string without the missing symbols.
     *
     * @return void
     */
    public function testUnphonetifyStringWithMissingSymbols() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->unphonetify('November Alfa Tango Oscar Colon'), 'NATO');
    }

    /**
     * Convert an alphabet to case-sensitive and convert a string to phonetics.
     *
     * @return void
     */
    public function testPhonetifyStringCaseSensitive() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $alpha->set_case_sensitivity(true);
        $alpha->add_symbol('a', 'alfa');
        $alpha->add_symbol('n', 'november');
        $alpha->add_symbol('o', 'oscar');
        $alpha->add_symbol('t', 'tango');
        $this->assertEquals($alpha->phonetify('NaTo'), 'November alfa Tango oscar');
    }

    /**
     * Convert a string containing extraneous whitespace to phonetics.
     *
     * @return void
     */
    public function testPhonetifyStringWithExtraneousWhitespace() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->phonetify("NA \tT\tO"), 'November Alfa Tango Oscar');
    }

    /**
     * Convert a string containing newlines to phonetics.
     *
     * @return void
     */
    public function testPhonetifyStringWithNewlines() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->phonetify("NA\nTO"), "November Alfa\nTango Oscar");
    }

    /**
     * Convert a string to phonetics with an alphabet containing a multiword symbol representation.
     *
     * @return void
     */
    public function testPhonetifyStringMultiword() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $alpha->add_symbol('N', 'New York');
        $this->assertEquals($alpha->phonetify('NATO'), 'New York Alfa Tango Oscar');
    }

    /**
     * Convert an alphabet to case-sensitive and convert a phonetic string to a string.
     *
     * @return void
     */
    public function testUnphonetifyStringCaseSensitive() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $alpha->set_case_sensitivity(true);
        $alpha->add_symbol('a', 'alfa');
        $alpha->add_symbol('n', 'november');
        $alpha->add_symbol('o', 'oscar');
        $alpha->add_symbol('t', 'tango');
        $this->assertEquals($alpha->unphonetify('November alfa Tango oscar'), 'NaTo');
    }

    /**
     * Convert a phonetic string containing newlines to a string.
     *
     * @return void
     */
    public function testUnphonetifyStringWithNewlines() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $this->assertEquals($alpha->unphonetify("November Alfa\nTango Oscar"), "NA\nTO");
    }

    /**
     * Convert a phonetic string to a string with an alphabet containing a multiword symbol representation.
     *
     * @return void
     */
    public function testUnphonetifyStringMultiword() {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json')));
        $alpha->add_symbol('N', 'New York');
        $this->assertEquals($alpha->unphonetify('New York Alfa Tango Oscar'), 'NATO');
    }
}
