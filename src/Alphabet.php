<?php

/**
 * This file is part of the Alphony package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Worthwelle\Alphonic;

use Worthwelle\Alphonic\Exception\InvalidAlphabetException;

/**
 * Represents an alphabet as defined in the Alphony phonetic alphabet schema and the encoding/decoding (phonification/unphonification) of strings for a particular alphabet.
 */
class Alphabet {
    /**
     * Contains a reference code for the alphabet.
     *
     * Used when multiple alphabets are loaded into Alphonic to ensure it is easy to find or reference a particular alphabet.
     *
     * @var string
     */
    public $code;
    /**
     * Keeps track of whether or not the alphabet has been changed since the last cache update.
     *
     * @var bool
     */
    private $dirty = true;
    /**
     * Contains the title of the alphabet.
     *
     * @var string
     */
    protected $title;
    /**
     * Contains the description of the alphabet.
     *
     * @var string
     */
    protected $description;
    /**
     * Contains the source of the alphabet.
     *
     * @var string
     */
    protected $source;
    /**
     * Contains the symbols and phonetic representations of the alphabet.
     *
     * @var array
     */
    protected $alphabet;
    /**
     * Contains a list of representations of more than one word, grouped by number of words.
     *
     * @var array
     */
    protected $multiword = array();
    /**
     * Contains the reverse of $alphabet to allow unphonetifying strings.
     *
     * @var array
     */
    protected $unalphabet;
    /**
     * A cache of a case-insensitive version of $unalphabet.
     *
     * @uses $unalphabet
     *
     * @var array
     */
    protected $unalphabet_i;
    /**
     * Keeps track of whether the alphabet is case sensitive.
     *
     * @var int
     */
    protected $case_sensitive;

    /**
     * Creates an alphabet from a PHP object representing a JSON object.
     *
     * Validates the provided PHP object, initializes the local variables of the Alphabet class and ensures that they are properly formatted.
     *
     * @param object $json a PHP object representation of a JSON object used to build the alphabet
     */
    public function __construct($json) {
        // Validates the JSON object and throws an exception if it is invalid.
        if ($json == null || !$this->validate_json($json)) {
            throw new InvalidAlphabetException();
        }

        $this->add_symbols($json->alphabets->en);
        $this->code = strtoupper($json->code);
        $this->title = is_array($json->title->en) ? $json->title->en[0] : $json->title->en;
        if (isset($json->description)) {
            $this->description = $json->description;
        }
        if (isset($json->source)) {
            $this->source = $json->source;
        }
        $this->case_sensitive = isset($json->case_sensitive) ? $json->case_sensitive : false;
    }

    /**
     * Creates an alphabet from a string containing a JSON object.
     *
     * Does validation checks on the json_decode itself and passes the generated PHP object to the Alphabet constructor.
     *
     * @param string $json a string containing the JSON object used to build the alphabet
     *
     * @return Alphabet a validated Alphabet
     */
    public static function from_json($json) {
        // Validates the JSON decode and throws an exception if it is invalid.
        $decoded_json = json_decode($json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidAlphabetException();
        }

        return new Alphabet($decoded_json);
    }

    /**
     * Creates an alphabet from a file containing a JSON object.
     *
     * Retrieves the contents of the file and passes them to the from_json function for further processing.
     *
     * @param string $filename the path to the file containing the JSON object
     *
     * @return Alphabet a validated Alphabet
     */
    public static function from_file($filename) {
        return Alphabet::from_json(file_get_contents($filename));
    }

    /**
     * Validates a PHP object representing a JSON object to ensure it follows the defined schema.
     *
     * @param object $json a PHP object representation of a JSON object used to build the alphabet
     *
     * @return bool the validation result of the object
     */
    public function validate_json($json) {
        $validator = new \JsonSchema\Validator();
        $validator->validate($json, (object) array('$ref' => 'file://' . __DIR__ . '/../resources/alphabet_schema.json'));

        return $validator->isValid();
    }

    /**
     * Sets the case-sensitivity of the alphabet.
     *
     * Changing this will likely cause issues or will require importing a large collection of new symbols.
     *
     * @param bool $case whether or not the alphabet is case-sensitive
     */
    public function set_case_sensitivity($case) {
        $this->case_sensitive = $case;
        $this->dirty = true;
    }

    /**
     * Adds an array of symbols to the alphabet.
     *
     * @param array $alphabet  an array of symbol and representation pairs to add to the alphabet
     * @param bool  $overwrite whether or not to overwrite existing symbols
     */
    public function add_symbols($alphabet, $overwrite = true) {
        foreach ($alphabet as $key => $value) {
            $this->add_symbol($key, $value, $overwrite);
        }
    }

    /**
     * Adds a single symbol to the alphabet.
     *
     * @param string $symbol         the symbol to add to the alphabet
     * @param string $representation the phonetic representation of the symbol provided
     * @param bool   $overwrite      whether or not to overwrite an existing symbol
     *
     * @return bool whether or not the symbol was successfully added
     */
    public function add_symbol($symbol, $representation, $overwrite = true) {
        // if the alphabet is not case-sensitive, force the symbol to uppercase to choose a standard case
        if (!$this->case_sensitive) {
            $symbol = strtoupper($symbol);
        }
        if (!$overwrite && isset($this->alphabet[$symbol])) {
            return false;
        }

        $representation = $this->clean_whitespace($representation);
        $count = count(explode(' ', $representation));
        $this->alphabet[$symbol] = $representation;
        if ($count > 1) {
            $clean_representation = $this->clean_representation($representation);
            $this->multiword[$count][] = $representation;
            $this->unalphabet[$clean_representation] = $symbol;
        } else {
            $this->unalphabet[$representation] = $symbol;
        }
        $this->dirty = true;

        return isset($this->alphabet[$symbol]);
    }

    /**
     * Gets the phonetic representation of a given symbol.
     *
     * @param string $symbol         the symbol to retrieve from the alphabet
     * @param bool   $return_missing whether or not to return the original symbol if there is no matching representation listed in the alphabet
     *
     * @return string|null the representation of the requested symbol or null if non-existent and $return_missing is false
     */
    public function get_symbol_represenation($symbol, $return_missing = false) {
        // if the alphabet is not case-sensitive, force the symbol to uppercase to choose a standard case
        if (!$this->case_sensitive) {
            $symbol = strtoupper($symbol);
        }
        if (!isset($this->alphabet[$symbol])) {
            if ($return_missing) {
                return $symbol;
            }

            return null;
        }

        return $this->alphabet[$symbol];
    }

    /**
     * Gets the symbol matching a given phonetic representation.
     *
     * @param string $representation the representation to retrieve from the alphabet
     * @param bool   $return_missing whether or not to return the original representation if there is no matching symbol listed in the alphabet
     *
     * @return string|null the representation of the requested symbol or null if non-existent and $return_missing is false
     */
    public function get_symbol_from_represenation($representation, $return_missing = false) {
        $search_array = $this->unalphabet;
        // if the alphabet is not case-sensitive, force the symbol and unalphabet to uppercase to choose a standard case
        if (!$this->case_sensitive) {
            $search_array = $this->get_caseinsensitive_unalphabet();
            $representation = strtoupper($representation);
        }

        if (!isset($search_array[$representation])) {
            if ($return_missing) {
                return $representation;
            }

            return null;
        }

        return $search_array[$representation];
    }

    /**
     * Removes spaces from a symbol representation.
     *
     * @return string the representation without spaces
     */
    public function clean_representation($representation) {
        return str_replace(' ', '-', $representation);
    }

    /**
     * Gets the case-insensitive version of the unalphabet.
     *
     * If the cache is outdated (the alphabet is "dirty"), rebuild the cache. Otherwise, return the existing cache.
     *
     * @return array the case-insensitive unalphabet
     */
    public function get_caseinsensitive_unalphabet() {
        if ($this->dirty) {
            $this->unalphabet_i = array();
            foreach ($this->unalphabet as $key => $val) {
                $this->unalphabet_i[strtoupper($key)] = strtoupper($val);
            }
        }

        return $this->unalphabet_i;
    }

    /**
     * Get the title or titles of the alphabet
     *
     * @return string|array the title or titles of the alphabet
     */
    public function get_title() {
        if (is_array($this->title)) {
            foreach ($this->title as $title) {
                return $title;
            }
        }

        return $this->title;
    }

    /**
     * Get the code of the alphabet
     *
     * @return string the code of the alphabet
     */
    public function get_code() {
        return $this->code;
    }

    /**
     * Get the description of the alphabet
     *
     * @return string the description of the alphabet
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Get the source of the alphabet
     *
     * @return string the source of the alphabet
     */
    public function get_source() {
        return $this->source;
    }

    /**
     * Encode (phonetify) a string into its phonetic representation.
     *
     * @param string $string         the string to encode
     * @param bool   $return_missing whether or not to return the original symbol if there is no matching representation listed in the alphabet
     *
     * @return string the phonetic representation of the given string
     */
    public function phonetify($string, $return_missing = false) {
        $string = $this->clean_whitespace($string);
        $lines = explode("\n", $string);
        $phonetic = array();
        if (count($lines) > 1) {
            $results = array();
            foreach ($lines as $line) {
                $results[] = $this->phonetify($line, $return_missing);
            }

            return implode("\n", $results);
        }
        foreach (str_split($string) as $char) {
            $symbol = $this->get_symbol_represenation($char, $return_missing);
            if ($symbol != null) {
                $phonetic[] = $symbol;
            }
        }

        return implode(' ', $phonetic);
    }

    /**
     * Decode (unphonetify) a string from its phonetic representation.
     *
     * @param string $string         the string to decode
     * @param bool   $return_missing whether or not to return the original representation if there is no matching symbol listed in the alphabet
     *
     * @return string the string of the given phonetic representation
     */
    public function unphonetify($string, $return_missing = false) {
        $string = $this->clean_whitespace($string);
        $string = $this->replace_multiword($string);
        $lines = explode("\n", $string);
        $phonetic = array();
        if (count($lines) > 1) {
            foreach ($lines as $line) {
                $phonetic[] = $this->unphonetify($line, $return_missing);
            }

            return implode("\n", $phonetic);
        }
        foreach (explode(' ', $string) as $rep) {
            $symbol = $this->get_symbol_from_represenation($rep, $return_missing);
            if ($symbol != null) {
                $phonetic[] = $symbol;
            }
        }

        return implode('', $phonetic);
    }

    /**
     * Replace all multi-word representations in a string with a cleaned version to allow for replacement.
     *
     * @param string $string the string to prepare
     *
     * @return string the prepared string
     */
    public function replace_multiword($string) {
        if (count($this->multiword) < 1) {
            return $string;
        }
        rsort($this->multiword);
        foreach ($this->multiword as $count) {
            foreach ($count as $word) {
                $string = str_replace($word, $this->clean_representation($word), $string);
            }
        }

        return $string;
    }

    /**
     * Standardize all whitespace in a given string.
     *
     * Replaces all characters other than a plain space or line feed character with a space, then removes leading and trailing spaces from around the line feeds.
     *
     * @param string $string the string to clean
     *
     * @return string the cleaned string
     */
    public function clean_whitespace($string) {
        $string = preg_replace("/[^\S\r\n ]+/", ' ', $string);
        $string = preg_replace("/ *(\r\n|\r|\n) */", '$1', $string);

        return preg_replace('/ +/', ' ', $string);
    }
}
