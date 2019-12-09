<?php

namespace Tests\Unit;
use Worthwelle\Alphonic\Alphabet;

use Tests\TestCase;

class AlphabetTest extends TestCase
{
    
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
    private $invalid_nato_json = <<<INVALID
{
    "code": 123,
    "title": {
        "en": [
            "NATO Phonetic Alphabet",
            "International Radiotelephony Spelling Alphabet",
            "ICAO phonetic alphabet"
        ]
    },
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
INVALID;
    
    /**
     * Load an alphabet.
     *
     * @return void
     */
    public function testLoadAlphabet()
    {
        $alpha = new Alphabet(json_decode($this->nato_json));
        $this->assertEquals($alpha->code, "NATO");
    }
    
    /**
     * Load an alphabet.
     *
     * @return void
     */
    public function testLoadInvalidAlphabet()
    {
        $this->expectException(\Worthwelle\Alphonic\Exception\InvalidAlphabetException::class);
        $alpha = new Alphabet(json_decode($this->invalid_nato_json));
    }
    
    public function testGetSymbolRepresentation()
    {
        $alpha = new Alphabet(json_decode($this->nato_json));
        $this->assertEquals($alpha->get_symbol_represenation("A"), "Alfa");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testConvertStringCaseInsensitive()
    {
        $alpha = new Alphabet(json_decode($this->nato_json));
        $this->assertEquals($alpha->get_string("nato"), "November Alfa Tango Oscar");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testConvertStringWithMissingSymbols()
    {
        $alpha = new Alphabet(json_decode($this->nato_json));
        $this->assertEquals($alpha->get_string("nato:"), "November Alfa Tango Oscar");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testAddSymbol()
    {
        $alpha = new Alphabet(json_decode($this->nato_json));
        $alpha->add_symbol(":","Colon");
        $this->assertEquals($alpha->get_string("nato:"), "November Alfa Tango Oscar Colon");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testAddSymbols()
    {
        $alpha = new Alphabet(json_decode($this->nato_json));
        $alpha->add_symbols([":" => "Colon", ";" => "Semicolon"]);
        $this->assertEquals($alpha->get_string("nato:;"), "November Alfa Tango Oscar Colon Semicolon");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testConvertStringCaseSensitive()
    {
        $alpha = new Alphabet(json_decode($this->nato_json));
        $alpha->set_case_sensitivity(true);
        $alpha->add_symbol("a","alfa");
        $alpha->add_symbol("n","november");
        $alpha->add_symbol("o","oscar");
        $alpha->add_symbol("t","tango");
        $this->assertEquals($alpha->get_string("NaTo"), "November alfa Tango oscar");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testGetTitle()
    {
        $alpha = new Alphabet(json_decode($this->nato_json));
        $this->assertEquals($alpha->get_title(), "NATO Phonetic Alphabet");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testGetDescription()
    {
        $alpha = new Alphabet(json_decode($this->nato_json));
        $this->assertEquals($alpha->get_description(), "The most widely used radiotelephone spelling alphabet. It is officially the International Radiotelephony Spelling Alphabet, and also commonly known as the ICAO phonetic alphabet, with a variation officially known as the ITU phonetic alphabet and figure code.");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testGetSource()
    {
        $alpha = new Alphabet(json_decode($this->nato_json));
        $this->assertEquals($alpha->get_source(), "https://www.icao.int/Pages/AlphabetRadiotelephony.aspx");
    }
}
