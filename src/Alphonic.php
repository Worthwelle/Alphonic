<?php

/**
 * This file is part of the Alphonic package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Worthwelle\Alphonic;

use Worthwelle\Alphonic\Exception\AlphabetNotFoundException;
use Worthwelle\Alphonic\Exception\InvalidAlphabetException;

/**
 * Manages a group of Alphabet objects to allow easily converting strings to and from various phonetic alphabets.
 */
class Alphonic {
    /**
     * Contains the alphabets currently loaded, keyed by reference code.
     *
     * @var string
     */
    public $alphabets = array();

    /**
     * Checks for alphabet files in the given directories
     *
     * Validates the provided PHP object, initializes the local variables of the Alphabet class and ensures that they are properly formatted.
     */
    public function load_alphabets($directories = null, $skip_invalid = false) {
        if ($directories === null) {
            $directories = array(__DIR__ . '/../alphabets');
        }
        if (is_string($directories)) {
            $directories = array($directories);
        }
        if (!is_array($directories)) {
            throw new \InvalidArgumentException();
        }
        foreach ($directories as $dir) {
            $files = glob("$dir/*.json");
            foreach ($files as $file) {
                try {
                    $this->add_alphabet_from_file($file);
                } catch (InvalidAlphabetException $e) {
                    if (!$skip_invalid) {
                        throw new InvalidAlphabetException($file);
                    }
                }
            }
        }
    }

    /**
     * Adds an alphabet from an Alphabet object.
     *
     * @param Alphabet $alpha the alphabet to add
     */
    public function add_alphabet_from_object($alpha) {
        $this->alphabets[$alpha->code] = $alpha;
    }

    /**
     * Adds an alphabet from a JSON string.
     *
     * @param string $json the JSON string representing the alphabet to add
     */
    public function add_alphabet_from_json($json) {
        $this->add_alphabet_from_object(Alphabet::from_json($json));
    }

    /**
     * Adds an alphabet from a JSON file.
     *
     * @param string $filename the path to a JSON file representing the alphabet to add
     */
    public function add_alphabet_from_file($filename) {
        $this->add_alphabet_from_object(Alphabet::from_file($filename));
    }

    /**
     * Retrieves the title of a given alphabet.
     *
     * @param string $alpha the reference code for the alphabet to retrieve
     *
     * @return string the title of the alphabet
     */
    public function get_title($alpha) {
        return $this->alphabet($alpha)->get_title();
    }

    /**
     * Retrieves the description of a given alphabet.
     *
     * @param string $alpha the reference code for the alphabet to retrieve
     *
     * @return string the description of the alphabet
     */
    public function get_description($alpha) {
        return $this->alphabet($alpha)->get_description();
    }

    /**
     * Retrieves the source of a given alphabet.
     *
     * @param string $alpha the reference code for the alphabet to retrieve
     *
     * @return string the source of the alphabet
     */
    public function get_source($alpha) {
        return $this->alphabet($alpha)->get_source();
    }

    /**
     * Retrieves all of the loaded alphabets.
     *
     * @return array the array of alphabet
     */
    public function get_alphabets() {
        return $this->alphabets;
    }

    /**
     * Encode (phonetify) a string into its phonetic representation using a given alphabet.
     *
     * @param string $string         the string to encode
     * @param string $alpha          the reference code for the desired alphabet
     * @param bool   $return_missing whether or not to return the original symbol if there is no matching representation listed in the alphabet
     *
     * @return string the phonetic representation of the given string
     */
    public function phonetify($string, $alpha, $return_missing = false) {
        return $this->alphabet($alpha)->phonetify($string, $return_missing);
    }

    /**
     * Decode (unphonetify) a string from its phonetic representation using a given alphabet.
     *
     * @param string $string         the string to dencode
     * @param string $alpha          the reference code for the desired alphabet
     * @param bool   $return_missing whether or not to return the original symbol if there is no matching representation listed in the alphabet
     *
     * @return string the string of the given phonetic representation
     */
    public function unphonetify($string, $alpha, $return_missing = false) {
        return $this->alphabet($alpha)->unphonetify($string, $return_missing);
    }

    /**
     * Retrieve a pointer to a given alphabet for direct editing.
     *
     * @param string $alpha the reference code for the desired alphabet
     *
     * @return Alphabet the desired alphabet
     */
    public function &alphabet($alpha) {
        $alpha = strtoupper($alpha);
        if (!isset($this->alphabets[$alpha])) {
            throw new AlphabetNotFoundException($alpha);
        }

        return $this->alphabets[$alpha];
    }
}
