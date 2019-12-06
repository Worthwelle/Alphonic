<?php

namespace Tests\Unit;
use Worthwelle\Alphonic\Alphonic;

use Tests\TestCase;

class AlphonicTest extends TestCase
{
    
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
}
