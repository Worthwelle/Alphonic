<?php

namespace Tests\Unit;
use Worthwelle\Alphonic\Alphonic;

use Tests\TestCase;

class AlphonicTest extends TestCase
{

    private $nato_json = <<<NATO
{
    "lang": ["en", "de"],
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
    "group": "NATO",
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
    public function testLoadAlphabets()
    {
        $alphonic = new Alphonic();
        $alphonic->load_alphabets();
        $this->assertEquals($alphonic->get_string("nato", "NATO"), "November Alfa Tango Oscar");
    }
    
    public function testLoadAlphabetFromJSON() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabet_from_json($nato_json);
        $this->assertEquals($alphonic->get_title("nato"), "NATO Phonetic Alphabet");
    }
    
    public function testLoadAlphabetFromFile() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabet_from_file(__DIR__.'/../alphabets/nato.json');
        $this->assertEquals($alphonic->get_title("nato"), "NATO Phonetic Alphabet");
    }
    
    public function testGetTitle() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabet_from_json($nato_json);
        $this->assertEquals($alphonic->get_title("nato"), "NATO Phonetic Alphabet");
    }
    
    public function testGetDescription() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabet_from_json($nato_json);
        $this->assertEquals($alphonic->get_description("nato"), "The most widely used radiotelephone spelling alphabet. It is officially the International Radiotelephony Spelling Alphabet, and also commonly known as the ICAO phonetic alphabet, with a variation officially known as the ITU phonetic alphabet and figure code.");
    }
    
    public function testGetSource() {
        $alphonic = new Alphonic();
        $alphonic->load_alphabet_from_json($nato_json);
        $this->assertEquals($alphonic->get_source("nato"), "https://www.icao.int/Pages/AlphabetRadiotelephony.aspx");
    }







}
