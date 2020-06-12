<?php

/**
 * This file is part of the Alphonic package.
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
     * Create a mocked up alphabet to test a specific function.
     *
     * @return Worthwelle\Alphonic\Alphabet
     */
    public function getMockNato($method = null, $return = null) {
        $decoded_json = json_decode(file_get_contents($this->root->url() . '/alphabets/nato.json'));

        $alpha = $this->getMockBuilder('\Worthwelle\Alphonic\Alphabet')
            ->disableOriginalConstructor()
            ->getMock();

        $alpha->expects($this->any())
            ->method('validate_json')
            ->will($this->returnValue(true));

        $alpha->expects($this->any())
            ->method('get_locales')
            ->will($this->returnValue(array('*', 'en')));

        $alpha->expects($this->any())
            ->method('get_default_locale')
            ->will($this->returnValue('*'));

        $alpha->expects($this->any())
            ->method('localize_object')
            ->will($this->returnValue((object) array(
                'en' => array(
                    'A' => 'Alfa',
                    'B' => 'Bravo',
                    'C' => 'Charlie',
                    'D' => 'Delta',
                    'E' => 'Echo',
                    'F' => 'Foxtrot',
                    'G' => 'Golf',
                    'H' => 'Hotel',
                    'I' => 'India',
                    'J' => 'Juliett',
                    'K' => 'Kilo',
                    'L' => 'Lima',
                    'M' => 'Mike',
                    'N' => 'November',
                    'O' => 'Oscar',
                    'P' => 'Papa',
                    'Q' => 'Quebec',
                    'R' => 'Romeo',
                    'S' => 'Sierra',
                    'T' => 'Tango',
                    'U' => 'Uniform',
                    'V' => 'Victor',
                    'W' => 'Whiskey',
                    'X' => 'Xray',
                    'Y' => 'Yankee',
                    'Z' => 'Zulu',
                    '1' => 'One',
                    '2' => 'Two',
                    '3' => 'Three',
                    '4' => 'Four',
                    '5' => 'Five',
                    '6' => 'Six',
                    '7' => 'Seven',
                    '8' => 'Eight',
                    '9' => 'Niner',
                    '0' => 'Zero'
                )
            )));

        $alpha->expects($this->any())
            ->method('localize_nonobject')
            ->will($this->returnValue(array(
                'en' => array(
                    'NATO Phonetic Alphabet'
                )
            )));

        $reflectedClass = new \ReflectionClass('\Worthwelle\Alphonic\Alphabet');
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($alpha, $decoded_json);

        if ($method !== null) {
            $alpha->expects($this->once())
                ->method($method)
                ->will($this->returnValue($return));
        }

        return $alpha;
    }

    /**
     * Validate included alphabets
     *
     * @return void
     */
    public function testValidateIncludedAlphabets() {
        $json_errors = Alphabet::get_json_errors();
        $files = Alphonic::streamSafeGlob(__DIR__ . '/../../alphabets/', '*.json');
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
        $this->assertContains('NATO Phonetic Alphabet', $alphonic->get_title('Alphonic-NATO'));
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
    public function testLoadAlphabetsFromDirectory() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabets($this->root->url() . '/alphabets');
        $this->assertEquals($alphonic->get_title('TESTALPHA'), 'NATO Phonetic Alphabet');
    }

    /**
     * Load a set of alphabets including invalid alphabets and verify an exception is thrown.
     *
     * @return void
     */
    public function testLoadInvalidAlphabets() {
        $this->expectException('\Worthwelle\Alphonic\Exception\InvalidAlphabetException');
        $alphonic = new Alphonic();
        $alphonic->load_alphabets(array($this->root->url() . '/invalid', $this->root->url() . '/alphabets'));
    }

    /**
     * Load a set of alphabets including invalid alphabets and verify the invalid alphabets are skipped.
     *
     * @return void
     */
    public function testLoadIgnoreInvalidAlphabets() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabets(array($this->root->url() . '/invalid', $this->root->url() . '/alphabets'), true);
        $this->assertGreaterThan(0, count($alphonic->get_alphabets()));
    }

    /**
     * Add an existing Alphabet object.
     *
     * @return void
     */
    public function testAddAlphabetFromObjectAndGetTitle() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_object($this->getMockNato('get_title', 'NATO Phonetic Alphabet'));
        $this->assertEquals($alphonic->get_title('TESTALPHA'), 'NATO Phonetic Alphabet');
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
        $alphonic->add_alphabet_from_json(file_get_contents($this->root->url() . '/alphabets/nato.json'));
        $this->assertEquals($alphonic->get_title('TESTALPHA'), 'NATO Phonetic Alphabet');
    }

    /**
     * Add an alphabet from a JSON file.
     *
     * @return void
     */
    public function testAddAlphabetFromFile() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/nato.json');
        $this->assertEquals($alphonic->get_title('TESTALPHA'), 'NATO Phonetic Alphabet');
    }

    /**
     * Search for an invalid locale.
     *
     * @return void
     */
    public function testSearchingInvalidLocale() {
        $this->expectException('\Worthwelle\Alphonic\Exception\InvalidLocaleException');
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/nato.json');
        $alphonic->locale_search('TESTALPHA', 'en-en-en-en');
    }

    /**
     * Search for a locale that matches exactly.
     *
     * @return void
     */
    public function testSearchingExactLocale() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/nato.json');
        $this->assertEquals($alphonic->locale_search('TESTALPHA', 'en'), 'en');
    }

    /**
     * Search for a locale that doesn't match, but has a parent locale. (ex: de-DE => de)
     *
     * @return void
     */
    public function testSearchingLocaleMatchingUmbrella() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/nato.json');
        $this->assertEquals($alphonic->locale_search('TESTALPHA', 'en-US'), 'en');
    }

    /**
     * Search for a locale that doesn't match, but sideshifts to a different locale in the same language. (ex: de-DE => de-AT)
     *
     * @return void
     */
    public function testSearchingSideshiftedLocale() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/sideshift.json');
        $this->assertEquals($alphonic->locale_search('LOCALES', 'en-US', true), 'en-CA');
    }

    /**
     * Search for a locale that is completely missing, but the alphabet has a wildcard locale.
     *
     * @return void
     */
    public function testSearchingMissingLocaleWithWildcard() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/wildcard.json');
        $this->assertEquals($alphonic->locale_search('WILDCARD', 'uz-Cyrl-UZ'), '*');
    }

    /**
     * Search for a locale that is completely missing and the alphabet doesn't have a wildcard locale.
     *
     * @return void
     */
    public function testSearchingMissingLocaleWithoutWildcard() {
        $this->expectException('\Worthwelle\Alphonic\Exception\LocaleNotFoundException');
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/sideshift.json');
        $alphonic->locale_search('LOCALES', 'en-US');
    }

    /**
     * Search for an umbrella locale in an alphabet that only has child locales. (ex: de =/> de-DE)
     *
     * @return void
     */
    public function testSearchingUmbrellaLocaleWithOnlyChildren() {
        $this->expectException('\Worthwelle\Alphonic\Exception\LocaleNotFoundException');
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/sideshift.json');
        $alphonic->locale_search('LOCALES', 'en');
    }

    /**
     * Encode (phonetify) a standard string using a given alphabet.
     *
     * @return void
     */
    public function testPhonetifyString() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_object($this->getMockNato('phonetify', 'Tango Echo Sierra Tango India November Golf'));
        $this->assertEquals($alphonic->phonetify('Testing', 'TESTALPHA'), 'Tango Echo Sierra Tango India November Golf');
    }

    /**
     * Decode (unphonetify) a standard string using a given alphabet.
     *
     * @return void
     */
    public function testUnphonetifyString() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_object($this->getMockNato('unphonetify', 'TESTING'));
        $this->assertEquals($alphonic->unphonetify('Tango Echo Sierra Tango India November Golf', 'TESTALPHA'), 'TESTING');
    }

    /**
     * Encode (phonetify) a standard string using a given alphabet using a non-default locale.
     *
     * @return void
     */
    public function testPhonetifyStringWithNondefault() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/wildcard.json');
        $this->assertEquals($alphonic->phonetify('AB', 'WILDCARD', 'en-GB'), 'Alfa Bravo');
    }

    /**
     * Decode (unphonetify) a standard string using a given alphabet using a non-default locale.
     *
     * @return void
     */
    public function testUnphonetifyStringWithNondefault() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/wildcard.json');
        $this->assertEquals($alphonic->unphonetify('Alfa Bravo', 'WILDCARD', 'en-GB'), 'AB');
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
        $alphonic->add_alphabet_from_object($this->getMockNato('get_description', 'Mock description'));
        $this->assertEquals($alphonic->get_description('TESTALPHA'), 'Mock description');
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
        $alphonic->add_alphabet_from_object($this->getMockNato('get_source', 'https://www.worthwelle.com/'));
        $this->assertEquals($alphonic->get_source('TESTALPHA'), 'https://www.worthwelle.com/');
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
        $alphonic->add_alphabet_from_object($this->getMockNato());
        $alphas = $alphonic->get_alphabets();
        $this->assertInstanceOf('Worthwelle\Alphonic\Alphabet', reset($alphas));
    }
}
