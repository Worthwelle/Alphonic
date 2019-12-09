<?php

namespace Tests\Unit;

use Worthwelle\Alphonic\Exception\InvalidAlphabetException;
use Worthwelle\Alphonic\Exception\AlphabetNotFoundException;

use Tests\TestCase;
use Worthwelle\Alphonic\Alphonic;

class AlphonicTest extends TestCase {
    private $nato_json = <<<NATO
{
    "code": "NATO",
    "title": {
        "en": [
            "NATO Phonetic Alphabet",
            "International Radiotelephony Spelling Alphabet",
            "ICAO phonetic alphabet"
        ]
    },
    "description": "The most widely used radiotelephone spelling alphabet. It is officially the International Radiotelephony Spelling Alphabet, and also commonly known as the ICAO phonetic alphabet, with a variation officially known as the ITU phonetic alphabet and figure code.",
    "source": "https://www.icao.int/Pages/AlphabetRadiotelephony.aspx",
    "alphabets": {
        "en": {
            "A": "Alfa",
            "B": "Bravo",
            "C": "Charlie",
            "D": "Delta",
            "E": "Echo",
            "F": "Foxtrot",
            "G": "Golf",
            "H": "Hotel",
            "I": "India",
            "J": "Juliett",
            "K": "Kilo",
            "L": "Lima",
            "M": "Mike",
            "N": "November",
            "O": "Oscar",
            "P": "Papa",
            "Q": "Quebec",
            "R": "Romeo",
            "S": "Sierra",
            "T": "Tango",
            "U": "Uniform",
            "V": "Victor",
            "W": "Whiskey",
            "X": "Xray",
            "Y": "Yankee",
            "Z": "Zulu",
            "1": "One",
            "2": "Two",
            "3": "Three",
            "4": "Four",
            "5": "Five",
            "6": "Six",
            "7": "Seven",
            "8": "Eight",
            "9": "Niner",
            "0": "Zero"
        }
    }
}
NATO;

    /**
     * Load alphabets.
     *
     * @return void
     */
    public function testLoadAlphabets() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabets();
        $this->assertEquals($alphonic->phonetify('nato', 'NATO'), 'November Alfa Tango Oscar');
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
        $alphonic->add_alphabet_from_json($this->nato_json);
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    public function testAddAlphabetFromFile() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file(__DIR__ . '/../../alphabets/nato.json');
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    public function testGetTitle() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json($this->nato_json);
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    public function testGetTitleFromInvalidAlphabet() {
        $alphonic = new Alphonic();
        $this->expectException(AlphabetNotFoundException::class);
        $alphonic->get_title('nato');
    }

    public function testGetDescription() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json($this->nato_json);
        $this->assertEquals($alphonic->get_description('nato'), 'The most widely used radiotelephone spelling alphabet. It is officially the International Radiotelephony Spelling Alphabet, and also commonly known as the ICAO phonetic alphabet, with a variation officially known as the ITU phonetic alphabet and figure code.');
    }

    public function testGetDescriptionFromInvalidAlphabet() {
        $alphonic = new Alphonic();
        $this->expectException(AlphabetNotFoundException::class);
        $alphonic->get_description('nato');
    }

    public function testGetSource() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_json($this->nato_json);
        $this->assertEquals($alphonic->get_source('nato'), 'https://www.icao.int/Pages/AlphabetRadiotelephony.aspx');
    }

    public function testGetSourceFromInvalidAlphabet() {
        $alphonic = new Alphonic();
        $this->expectException(AlphabetNotFoundException::class);
        $alphonic->get_source('nato');
    }
}
