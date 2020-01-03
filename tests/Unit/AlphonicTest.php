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
use Worthwelle\Alphonic\Alphonic;

class AlphonicTest extends TestCase {
    /**
     * Validate included alphabets
     *
     * @return void
     */
    public function testValidateIncludedAlphabets() {
        $constants = get_defined_constants(true);
        $json_errors = array();
        if (isset($constants['json'])) {
            $json_codes = $constants['json'];
        } else {
            $json_codes = $constants['Core'];
        }
        foreach ($json_codes as $name => $value) {
            if (!strncmp($name, 'JSON_ERROR_', 11)) {
                $json_errors[$value] = $name;
            }
        }
        unset($constants);

        $files = glob(__DIR__ . '/../../alphabets/*.json');
        foreach ($files as $file) {
            $json = @file_get_contents($file);
            $this->assertNotFalse($json, "Could not open file: $file");

            $decoded_json = json_decode($json);
            $this->assertEquals(json_last_error(), JSON_ERROR_NONE, "Could not decode file: $file. Error: " . $json_errors[json_last_error()]);
            $validator = new \JsonSchema\Validator();
            $validator->validate($decoded_json, (object) array('$ref' => 'file://' . __DIR__ . '/../../resources/alphabet_schema.json'));

            if (count($validator->getErrors()) > 0) {
                $error_array = $validator->getErrors();
                if (is_array($error_array[0])) {
                    $error = $error_array[0];
                }
            } else {
                $error = array('property' => '', 'message' => '');
            }

            $this->assertTrue($validator->isValid(), "Could not validate file: $file. First error: [" . $error['property'] . '] ' . $error['message']);

            $this->assertInstanceOf("Worthwelle\Alphonic\Alphabet", Alphabet::from_file($file));
        }
    }

    /**
     * Load the standard alphabets.
     *
     * @depends testValidateIncludedAlphabets
     *
     * @return void
     */
    public function testLoadAlphabets() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabets();
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    /**
     * Load alphabets with a bad directory argument.
     *
     * @return void
     */
    public function testLoadAlphabetsWithBadArgument() {
        $this->expectException('\InvalidArgumentException');
        $alphonic = new Alphonic();
        $alphonic->load_alphabets(123);
    }

    /**
     * Load a set of alphabets including invalid alphabets and verify an exception is thrown.
     *
     * @return void
     */
    public function testLoadInvalidAlphabets() {
        $this->expectException('\Worthwelle\Alphonic\Exception\InvalidAlphabetException');
        $alphonic = new Alphonic();
        $alphonic->load_alphabets(__DIR__ . '/../../resources/test_alphabets');
    }

    /**
     * Load a set of alphabets including invalid alphabets and verify the invalid alphabets are skipped.
     *
     * @return void
     */
    public function testLoadIgnoreInvalidAlphabets() {
        $alphonic = new Alphonic();
        $this->assertNull($alphonic->load_alphabets(__DIR__ . '/../../resources/test_alphabets', true));
    }

    /**
     * Add an existing Alphabet object.
     *
     * @return void
     */
    public function testAddAlphabetFromObject() {
        $alpha = Alphabet::from_file(__DIR__ . '/../../resources/test_alphabets/valid_nato.json');
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_object($alpha);
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    /**
     * Add an alphabet from a JSON string.
     *
     * @testdox Add alphabet from JSON
     *
     * @return void
     */
    public function testAddAlphabetFromJSON() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    /**
     * Add an alphabet from a JSON file.
     *
     * @return void
     */
    public function testAddAlphabetFromFile() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file(__DIR__ . '/../../alphabets/nato.json');
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    /**
     * Encode (phonetify) a standard string using a given alphabet.
     *
     * @return void
     */
    public function testPhonetifyString() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->phonetify('Testing', 'nato'), 'Tango Echo Sierra Tango India November Golf');
    }

    /**
     * Decode (unphonetify) a standard string using a given alphabet.
     *
     * @return void
     */
    public function testUnphonetifyString() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->unphonetify('Tango Echo Sierra Tango India November Golf', 'nato'), 'TESTING');
    }

    /**
     * Retrieve the title of an alphabet.
     *
     * @return void
     */
    public function testGetTitle() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    /**
     * Retrieve the title of a missing alphabet and verify an exception is thrown.
     *
     * @return void
     */
    public function testGetTitleFromInvalidAlphabet() {
        $alphonic = new Alphonic();
        $this->expectException('\Worthwelle\Alphonic\Exception\AlphabetNotFoundException');
        $alphonic->get_title('nato');
    }

    /**
     * Retrieve the description of an alphabet.
     *
     * @return void
     */
    public function testGetDescription() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->get_description('nato'), 'The most widely used radiotelephone spelling alphabet. It is officially the International Radiotelephony Spelling Alphabet, and also commonly known as the ICAO phonetic alphabet, with a variation officially known as the ITU phonetic alphabet and figure code.');
    }

    /**
     * Retrieve the description of a missing alphabet and verify an exception is thrown.
     *
     * @return void
     */
    public function testGetDescriptionFromInvalidAlphabet() {
        $alphonic = new Alphonic();
        $this->expectException('\Worthwelle\Alphonic\Exception\AlphabetNotFoundException');
        $alphonic->get_description('nato');
    }

    /**
     * Retrieve the source of an alphabet.
     *
     * @return void
     */
    public function testGetSource() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->get_source('nato'), 'https://www.icao.int/Pages/AlphabetRadiotelephony.aspx');
    }

    /**
     * Retrieve the source of a missing alphabet and verify an exception is thrown.
     *
     * @return void
     */
    public function testGetSourceFromInvalidAlphabet() {
        $alphonic = new Alphonic();
        $this->expectException('\Worthwelle\Alphonic\Exception\AlphabetNotFoundException');
        $alphonic->get_source('nato');
    }

    /**
     * Retrieve the alphabets from an Alphonic instance.
     *
     * @depends testValidateIncludedAlphabets
     *
     * @return void
     */
    public function testGetAlphabets() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $alphas = $alphonic->get_alphabets();
        $this->assertInstanceOf('Worthwelle\Alphonic\Alphabet', reset($alphas));
    }
}
