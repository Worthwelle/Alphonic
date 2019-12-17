<?php

namespace Tests\Unit;

use Tests\TestCase;
use Worthwelle\Alphonic\Alphonic;
use Worthwelle\Alphonic\Exception\AlphabetNotFoundException;
use Worthwelle\Alphonic\Exception\InvalidAlphabetException;

class AlphonicTest extends TestCase {
    /**
     * Load alphabets.
     *
     * @return void
     */
    public function testLoadAlphabets() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabets();
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    public function testLoadInvalidAlphabets() {
        $this->expectException(InvalidAlphabetException::class);
        $alphonic = new Alphonic();
        $alphonic->load_alphabets(array(__DIR__ . '/../../resources/test_alphabets'));
    }

    public function testLoadIgnoreInvalidAlphabets() {
        $alphonic = new Alphonic();
        $this->assertNull($alphonic->load_alphabets(array(__DIR__ . '/../../resources/test_alphabets'), true));
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
}
