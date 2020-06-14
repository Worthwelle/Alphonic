<?php

namespace Tests;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class BaseTestCase extends PHPUnitTestCase {

        public $structure = array(
            'alphabets' => array(
                'nato.json'             => '{"code": "TESTALPHA","title": {"en": "NATO Phonetic Alphabet"},"description": "A test alphabet.","source": "http://www.worthwelle.com","alphabets": {"en": {"A": "Alfa","N": "November","T": "Tango","O": "Oscar"}}}',
                'sideshift.json'        => '{"code": "LOCALES","alphabets": {"en-GB": {"A": "Alfa", "B": "Bravo"}, "en-CA": {"A": "Alfa", "B": "Bravo"}}}',
                'wildcard.json'         => '{"code": "WILDCARD","alphabets": {"*": {"A": "Alpha", "B": "Bravo"}, "en-GB": {"A": "Alfa", "B": "Bravo"}, "en-CA": {"A": "Alfa", "B": "Bravo"}}}',
                'unicode_alpha.json'    => '{"code": "BRAILLE-FR","alphabets": {"en": {"I": "⠊","J": "⠚"}}}',
                'no_locale_alpha.json'  => '{"code": "NOLOCALE","title": {"en": "NATO Phonetic Alphabet"},"description": "A test alphabet.","alphabets": {"A": "Alfa","N": "November","T": "Tango","O": "Oscar"}}',
                'two_locale_alpha.json' => '{"code": "TWOLOCALE","title": {"en": ["NATO Phonetic Alphabet","Another Title"]},"description": "A test alphabet.","alphabets": {"en": {"A": "Alfa","N": "November","T": "Tango","O": "Oscar"}, "*": {"A": "Alfa","N": "November","T": "Tango","O": "Oscar", ":": "Colon"}}}',
            ),
            'alphabets_alt' => array(
                'nato2.json'    => '{"code": "TESTALPHA2","title": {"en": "NATO Phonetic Alphabet"},"description": "A test alphabet.","alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie",}}}',
            ),
            'invalid' => array(
                'invalid_nato.json' => '{"code": 123,"title": {"en": "NATO Phonetic Alphabet"},"alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie"}}}',
                'invalid_json.json' => '{"code": "INVALIDJSON","title": {"en": "NATO Phonetic Alphabet"},"alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie"',
            )
        );
}
