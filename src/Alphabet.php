<?php

namespace Worthwelle\Alphonic;

use Worthwelle\Alphonic\Exception\InvalidAlphabetException;

class Alphabet {
    public $code;
    private $dirty = true;
    protected $title;
    protected $description;
    protected $source;
    protected $alphabet;
    protected $unalphabet;
    protected $unalphabet_i;
    protected $case_sensitive;

    // Constructor
    public function __construct($json) {
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

    public static function from_json($json) {
        $decoded_json = json_decode($json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidAlphabetException();
        }

        return new Alphabet($decoded_json);
    }

    public static function from_file($filename) {
        return Alphabet::from_json(file_get_contents($filename));
    }

    public function validate_json($json) {
        $validator = new \JsonSchema\Validator();
        $validator->validate($json, (object) array('$ref' => 'file://' . __DIR__ . '/../resources/alphabet_schema.json'));

        return $validator->isValid();
    }

    public function add_symbols($alphabet) {
        foreach ($alphabet as $key => $value) {
            $this->add_symbol($key, $value);
        }
    }

    public function set_case_sensitivity($case) {
        $this->case_sensitive = $case;
        $this->dirty = true;
    }

    public function add_symbol($symbol, $representation, $ipa = null) {
        if (!$this->case_sensitive) {
            $symbol = strtoupper($symbol);
        }
        $this->alphabet[$symbol] = array(
            $representation, $ipa
        );
        $this->unalphabet[$representation] = $symbol;
        $this->dirty = true;
    }

    public function get_symbol_represenation($symbol, $ipa = false) {
        if (!$this->case_sensitive) {
            $symbol = strtoupper($symbol);
        }
        if (!isset($this->alphabet[$symbol])) {
            return null;
        }
        if ($ipa) {
            return $this->alphabet[$symbol];
        }

        return $this->alphabet[$symbol][0];
    }

    public function get_symbol_from_represenation($rep) {
        $search_array = $this->unalphabet;
        if (!$this->case_sensitive) {
            if ($this->dirty) {
                $this->unalphabet_i = array();
                foreach ($this->unalphabet as $key => $val) {
                    $this->unalphabet_i[strtoupper($key)] = strtoupper($val);
                }
                $rep = strtoupper($rep);
            }
            $search_array = $this->unalphabet_i;
        }

        if (!isset($search_array[$rep])) {
            return null;
        }

        return $search_array[$rep];
    }

    public function get_title() {
        if (is_array($this->title)) {
            foreach ($this->title as $title) {
                return $title;
            }
        }

        return $this->title;
    }

    public function get_description() {
        return $this->description;
    }

    public function get_source() {
        return $this->source;
    }

    public function phonetify($string) {
        $string = $this->clean_whitespace($string);
        $lines = explode("\n", $string);
        $phonetic = array();
        if (count($lines) > 1) {
            $results = array();
            foreach ($lines as $line) {
                $results[] = $this->phonetify($line);
            }

            return implode("\n", $results);
        }
        foreach (str_split($string) as $char) {
            $symbol = $this->get_symbol_represenation($char);
            if ($symbol != null) {
                $phonetic[] = $symbol;
            }
        }

        return implode(' ', $phonetic);
    }

    public function unphonetify($string) {
        $string = $this->clean_whitespace($string);
        $lines = explode("\n", $string);
        $phonetic = array();
        if (count($lines) > 1) {
            foreach ($lines as $line) {
                $phonetic[] = $this->unphonetify($line);
            }

            return implode("\n", $phonetic);
        }
        foreach (explode(' ', $string) as $rep) {
            $symbol = $this->get_symbol_from_represenation($rep);
            if ($symbol != null) {
                $phonetic[] = $symbol;
            }
        }

        return implode('', $phonetic);
    }

    public function clean_whitespace($string) {
        $string = preg_replace("/[^\S\n ]+/", ' ', $string);
        $string = preg_replace("/ *\n[\n ]+/", "\n", $string);

        return preg_replace('/ +/', ' ', $string);
    }
}
