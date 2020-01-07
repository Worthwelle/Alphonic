<?php

/**
 * This file is part of the Alphonic package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Tests\TestCase;
use Worthwelle\Alphonic\Alphabet;
use Worthwelle\Alphonic\Alphonic;

class AlphonicTest extends TestCase {
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * Set up test environmemt
     */
    public function setUp() {
        $structure = array(
            'alphabets' => array(
                'nato.json'    => '{"code": "NATO","title": {"en": "NATO Phonetic Alphabet"},"description": "A test alphabet.","source": "http://www.worthwelle.com","alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie","D": "Delta","E": "Echo","F": "Foxtrot","G": "Golf","H": "Hotel","I": "India","J": "Juliett","K": "Kilo","L": "Lima","M": "Mike","N": "November","O": "Oscar","P": "Papa","Q": "Quebec","R": "Romeo","S": "Sierra","T": "Tango","U": "Uniform","V": "Victor","W": "Whiskey","X": "Xray","Y": "Yankee","Z": "Zulu","1": "One","2": "Two","3": "Three","4": "Four","5": "Five","6": "Six","7": "Seven","8": "Eight","9": "Niner","0": "Zero"}}}',
            ),
            'invalid' => array(
                'invalid_nato.json' => '{"code": 123,"title": {"en": "NATO Phonetic Alphabet"},"alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie"}}}',
                'invalid_json.json' => '{"code": "NATO2","title": {"en": "NATO Phonetic Alphabet"},"alphabets": {"en": {"A": "Alfa","B": "Bravo","C": "Charlie"',
            )
        );
        $this->root = vfsStream::setup('root', null, $structure);
    }

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
        $constants = get_defined_constants(true);
        $json_codes = array();
        // HHVM loads constants defined by extensions under the "Core" key.
        // https://github.com/facebook/hhvm/issues/7402
        if (defined('HHVM_VERSION')) {
            $json_codes = $constants['Core'];
        } else {
            $json_codes = $constants['json'];
        }
        foreach ($json_codes as $name => $value) {
            if (!strncmp($name, 'JSON_ERROR_', 11)) {
                $json_errors[$value] = $name;
            }
        }
        unset($constants);

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
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
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
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
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
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    /**
     * Add an alphabet from a JSON file.
     *
     * @return void
     */
    public function testAddAlphabetFromFile() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_file($this->root->url() . '/alphabets/nato.json');
        $this->assertEquals($alphonic->get_title('nato'), 'NATO Phonetic Alphabet');
    }

    /**
     * Encode (phonetify) a standard string using a given alphabet.
     *
     * @return void
     */
    public function testPhonetifyString() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_object($this->getMockNato('phonetify', 'Tango Echo Sierra Tango India November Golf'));
        $this->assertEquals($alphonic->phonetify('Testing', 'nato'), 'Tango Echo Sierra Tango India November Golf');
    }

    /**
     * Decode (unphonetify) a standard string using a given alphabet.
     *
     * @return void
     */
    public function testUnphonetifyString() {
        $alphonic = new Alphonic();
        $alphonic->add_alphabet_from_object($this->getMockNato('unphonetify', 'TESTING'));
        $this->assertEquals($alphonic->unphonetify('Tango Echo Sierra Tango India November Golf', 'nato'), 'TESTING');
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
        $this->assertEquals($alphonic->get_description('nato'), 'Mock description');
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
        $this->assertEquals($alphonic->get_source('nato'), 'https://www.worthwelle.com/');
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
