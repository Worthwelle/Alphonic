<?php

namespace Tests\Unit;
use Worthwelle\Alphonic\Alphabet;

use Tests\TestCase;

class AlphabetTest extends TestCase
{
    
    /**
     * Load an alphabet.
     *
     * @return void
     */
    public function testLoadAlphabet()
    {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__."/../../alphabets/nato.json"), true));
        $this->assertEquals($alpha->code, "NATO");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testConvertStringCaseInsensitive()
    {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__."/../../alphabets/nato.json"), true));
        $this->assertEquals($alpha->get_string("nato"), "November Alpha Tango Oscar");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testConvertStringWithMissingSymbols()
    {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__."/../../alphabets/nato.json"), true));
        $this->assertEquals($alpha->get_string("nato:"), "November Alpha Tango Oscar");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testAddSymbol()
    {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__."/../../alphabets/nato.json"), true));
        $alpha->add_symbol(":","Colon");
        $this->assertEquals($alpha->get_string("nato:"), "November Alpha Tango Oscar Colon");
    }
    
    /**
     * Convert a string to phonetics.
     *
     * @return void
     */
    public function testConvertStringCaseSensitive()
    {
        $alpha = new Alphabet(json_decode(file_get_contents(__DIR__."/../../alphabets/nato.json"), true));
        $alpha->set_case_sensitivity(true);
        $alpha->add_symbol("a","alpha");
        $alpha->add_symbol("n","november");
        $alpha->add_symbol("o","oscar");
        $alpha->add_symbol("t","tango");
        $this->assertEquals($alpha->get_string("NaTo"), "November alpha Tango oscar");
    }
}
