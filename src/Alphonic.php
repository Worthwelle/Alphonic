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
use Worthwelle\Alphonic\Exception\InvalidLocaleException;
use Worthwelle\Alphonic\Exception\LocaleNotFoundException;

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
            $files = self::streamSafeGlob("$dir", '*.json');
            foreach ($files as $file) {
                try {
                    $this->add_alphabet_from_file($file);
                } catch (InvalidAlphabetException $e) {
                    if (!$skip_invalid) {
                        throw new InvalidAlphabetException($e->getMessage() . ' File: ' . $file);
                    }
                }
            }
        }
    }

    /**
     * Glob that is safe with streams (vfs for example)
     *
     * PHP's built-in glob function does not support stream wrappers.
     *
     * https://github.com/bovigo/vfsStream/issues/2
     *
     * @param string $directory
     * @param string $filePattern
     *
     * @return array
     */
    public static function streamSafeGlob($directory, $filePattern) {
        $files = scandir($directory);
        $found = array();

        foreach ($files as $filename) {
            if (in_array($filename, array('.', '..'))) {
                continue;
            }

            if (fnmatch($filePattern, $filename)) {
                $found[] = "{$directory}/{$filename}";
            }
        }

        return $found;
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
    public function get_title($alpha, $locale = '') {
        return $this->alphabet($alpha)->get_title($locale);
    }

    /**
     * Retrieves the description of a given alphabet.
     *
     * @param string $alpha the reference code for the alphabet to retrieve
     *
     * @return string the description of the alphabet
     */
    public function get_description($alpha, $locale = '') {
        return $this->alphabet($alpha)->get_description($locale);
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
     * @return array the array of alphabets
     */
    public function get_alphabets() {
        return $this->alphabets;
    }

    /**
     * Find the closest matching locale from the given alphabet.
     *
     * @param string $alpha     the reference code for the desired alphabet
     * @param string $locale    the reference code for the desired l
     * @param string $sideshift whether to find a match in different locales in the same language
     *
     * @return string the closest matching locale
     */
    public function locale_search($alpha, $locale, $sideshift = false) {
        if ($locale == '') {
            return '';
        }
        $locales = array_map('Worthwelle\Alphonic\Alphabet::format_locale', $this->alphabet($alpha)->get_locales());
        $locale = Alphabet::format_locale($locale);

        if (in_array($locale, $locales)) {
            return $locale;
        }
        $split = explode('-', $locale);

        switch (count($split)) {
            case 1:
                $language = $split[0];
                $territory = null;
                break;
            case 2:
                list($language, $territory) = $split;
                break;
            case 3:
                $language = $split[0];
                $territory = $split[1] . '-' . $split[2];
                break;
            default:
                throw new InvalidLocaleException("$locale is not a valid locale");
        }

        if ($territory != null) {
            if ($sideshift) {
                $related = $this->find_language_locales($locales, $language);

                return Alphabet::format_locale($related[0]);
            }
            if (in_array($language, $locales)) {
                return $language;
            }
        }

        if (in_array('*', $locales)) {
            return '*';
        }

        throw new LocaleNotFoundException("Locale $locale was not found in $alpha");
    }

    /**
     * Search for locales within a given language
     *
     * @param string $locales  the list of locale reference codes to search
     * @param string $language the reference code for the desired language
     *
     * @return array an array of matching locale reference codes
     */
    public function find_language_locales($haystack, $language) {
        $matches = array();
        foreach ($haystack as $locale) {
            if (strpos($locale, $language) === 0) {
                $matches[] = $locale;
            }
        }

        return $matches;
    }

    /**
     * Encode (phonetify) a string into its phonetic representation using a given alphabet.
     *
     * @param string $string         the string to encode
     * @param string $alpha          the reference code for the desired alphabet
     * @param string $locale         the reference code for the desired locale
     * @param bool   $return_missing whether or not to return the original symbol if there is no matching representation listed in the alphabet
     *
     * @return string the phonetic representation of the given string
     */
    public function phonetify($string, $alpha, $locale = '', $return_missing = false) {
        $locale = $this->locale_search($alpha, $locale);

        return $this->alphabet($alpha)->phonetify($string, $locale, $return_missing);
    }

    /**
     * Decode (unphonetify) a string from its phonetic representation using a given alphabet.
     *
     * @param string $string         the string to dencode
     * @param string $alpha          the reference code for the desired alphabet
     * @param string $locale         the reference code for the desired locale
     * @param bool   $return_missing whether or not to return the original symbol if there is no matching representation listed in the alphabet
     *
     * @return string the string of the given phonetic representation
     */
    public function unphonetify($string, $alpha, $locale = '', $return_missing = false) {
        $locale = $this->locale_search($alpha, $locale);

        return $this->alphabet($alpha)->unphonetify($string, $locale, $return_missing);
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
