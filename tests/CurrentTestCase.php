<?php

namespace Tests;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class CurrentTestCase extends BaseTestCase {
    /**
     * @var vfsStreamDirectory
     */
    public $root;

    /**
     * Set up test environmemt
     */
    public function setUp(): void {
        $structure = array(
            'alphabets' => array(
                'nato.json'    => '{"code": "NATO","title": {"en": "NATO Phonetic Alphabet"},"description": "A test alphabet.","source": "http://www.worthwelle.com","alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie","D": "Delta","E": "Echo","F": "Foxtrot","G": "Golf","H": "Hotel","I": "India","J": "Juliett","K": "Kilo","L": "Lima","M": "Mike","N": "November","O": "Oscar","P": "Papa","Q": "Quebec","R": "Romeo","S": "Sierra","T": "Tango","U": "Uniform","V": "Victor","W": "Whiskey","X": "Xray","Y": "Yankee","Z": "Zulu","1": "One","2": "Two","3": "Three","4": "Four","5": "Five","6": "Six","7": "Seven","8": "Eight","9": "Niner","0": "Zero"}}}',
                'sideshift.json' => '{"code": "LOCALES","alphabets": {"en-GB": {"A": "Alfa", "B": "Bravo"}, "en-CA": {"A": "Alfa", "B": "Bravo"}}}',
                'wildcard.json' => '{"code": "WILDCARD","alphabets": {"*": {"A": "Alpha", "B": "Bravo"}, "en-GB": {"A": "Alfa", "B": "Bravo"}, "en-CA": {"A": "Alfa", "B": "Bravo"}}}',
                'unicode_alpha.json' => '{"code": "BRAILLE-FR","alphabets": {"en": {"I": "⠊","J": "⠚"}}}',
                'no_locale_alpha.json'    => '{"code": "NATONOLOCALE","title": {"en": "NATO Phonetic Alphabet"},"description": "A test alphabet.","source": "http://www.worthwelle.com","alphabets": {"A": "Alfa","B": "Bravo","C": "Charlie","D": "Delta","E": "Echo","F": "Foxtrot","G": "Golf","H": "Hotel","I": "India","J": "Juliett","K": "Kilo","L": "Lima","M": "Mike","N": "November","O": "Oscar","P": "Papa","Q": "Quebec","R": "Romeo","S": "Sierra","T": "Tango","U": "Uniform","V": "Victor","W": "Whiskey","X": "Xray","Y": "Yankee","Z": "Zulu","1": "One","2": "Two","3": "Three","4": "Four","5": "Five","6": "Six","7": "Seven","8": "Eight","9": "Niner","0": "Zero"}}',
                'two_locale_alpha.json'    => '{"code": "NATOTWO","title": {"en": ["NATO Phonetic Alphabet","Another Title"]},"description": "A test alphabet.","source": "http://www.worthwelle.com","alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie","D": "Delta","E": "Echo","F": "Foxtrot","G": "Golf","H": "Hotel","I": "India","J": "Juliett","K": "Kilo","L": "Lima","M": "Mike","N": "November","O": "Oscar","P": "Papa","Q": "Quebec","R": "Romeo","S": "Sierra","T": "Tango","U": "Uniform","V": "Victor","W": "Whiskey","X": "Xray","Y": "Yankee","Z": "Zulu","1": "One","2": "Two","3": "Three","4": "Four","5": "Five","6": "Six","7": "Seven","8": "Eight","9": "Niner","0": "Zero"}, "*": {"A": "Alfa","B": "Bravo","C": "Charlie","D": "Delta","E": "Echo","F": "Foxtrot","G": "Golf","H": "Hotel","I": "India","J": "Juliett","K": "Kilo","L": "Lima","M": "Mike","N": "November","O": "Oscar","P": "Papa","Q": "Quebec","R": "Romeo","S": "Sierra","T": "Tango","U": "Uniform","V": "Victor","W": "Whiskey","X": "Xray","Y": "Yankee","Z": "Zulu","1": "One","2": "Two","3": "Three","4": "Four","5": "Five","6": "Six","7": "Seven","8": "Eight","9": "Niner","0": "Zero", ":": "Colon"}}}',
            ),
            'alphabets_alt' => array(
                'nato2.json'    => '{"code": "NATO2","title": {"en": "NATO Phonetic Alphabet"},"description": "A test alphabet.","source": "http://www.worthwelle.com","alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie","D": "Delta","E": "Echo","F": "Foxtrot","G": "Golf","H": "Hotel","I": "India","J": "Juliett","K": "Kilo","L": "Lima","M": "Mike","N": "November","O": "Oscar","P": "Papa","Q": "Quebec","R": "Romeo","S": "Sierra","T": "Tango","U": "Uniform","V": "Victor","W": "Whiskey","X": "Xray","Y": "Yankee","Z": "Zulu","1": "One","2": "Two","3": "Three","4": "Four","5": "Five","6": "Six","7": "Seven","8": "Eight","9": "Niner","0": "Zero"}}}',
            ),
            'invalid' => array(
                'invalid_nato.json' => '{"code": 123,"title": {"en": "NATO Phonetic Alphabet"},"alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie"}}}',
                'invalid_json.json' => '{"code": "NATO2","title": {"en": "NATO Phonetic Alphabet"},"alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie"',
            )
        );
        $this->root = vfsStream::setup('root', null, $structure);
    }
}
