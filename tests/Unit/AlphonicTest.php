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
use Worthwelle\Alphonic\Exception\AlphabetNotFoundException;
use Worthwelle\Alphonic\Exception\InvalidAlphabetException;

class AlphonicTest extends TestCase {
    /**
     * Load the standard alphabets.
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
        $this->expectException(\InvalidArgumentException::class);
        $alphonic = new Alphonic();
        $alphonic->load_alphabets(123);
    }

    /**
     * Load a set of alphabets including invalid alphabets and verify an exception is thrown.
     *
     * @return void
     */
    public function testLoadInvalidAlphabets() {
        $this->expectException(InvalidAlphabetException::class);
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

    public function testAddAlphabetFromObject() {
        $alpha = Alphabet::from_file(__DIR__ . '/../../resources/test_alphabets/valid_nato.json');
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_object($alpha);
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    public function testAddAlphabetFromJSON() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    public function testAddAlphabetFromFile() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file(__DIR__ . '/../../alphabets/nato.json');
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    public function testPhonetifyString() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->phonetify('Testing', 'nato'), 'Tango Echo Sierra Tango India November Golf');
    }

    public function testUnphonetifyString() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->unphonetify('Tango Echo Sierra Tango India November Golf', 'nato'), 'TESTING');
    }

    public function testGetTitle() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    public function testGetTitleFromInvalidAlphabet() {
        $alphonic = new Alphonic();
        $this->expectException(AlphabetNotFoundException::class);
        $alphonic->get_title('nato');
    }

    public function testGetDescription() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->get_description('nato'), 'The most widely used radiotelephone spelling alphabet. It is officially the International Radiotelephony Spelling Alphabet, and also commonly known as the ICAO phonetic alphabet, with a variation officially known as the ITU phonetic alphabet and figure code.');
    }

    public function testGetDescriptionFromInvalidAlphabet() {
        $alphonic = new Alphonic();
        $this->expectException(AlphabetNotFoundException::class);
        $alphonic->get_description('nato');
    }

    public function testGetSource() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json(file_get_contents(__DIR__ . '/../../resources/test_alphabets/valid_nato.json'));
        $this->assertEquals($alphonic->get_source('nato'), 'https://www.icao.int/Pages/AlphabetRadiotelephony.aspx');
    }

    public function testGetSourceFromInvalidAlphabet() {
        $alphonic = new Alphonic();
        $this->expectException(AlphabetNotFoundException::class);
        $alphonic->get_source('nato');
    }

    public function testGetAlphabets() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabets();
        $alphas = $alphonic->get_alphabets();
        $this->assertInstanceOf('Worthwelle\Alphonic\Alphabet', reset($alphas));
    }
}
