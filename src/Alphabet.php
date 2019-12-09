<?php

namespace Worthwelle\Alphonic;

use Worthwelle\Alphonic\Exception\InvalidAlphabetException;

class Alphabet {
    public $code;
    private $title;
    private $description;
    private $source;
    private $alphabet;
    private $case_sensitive;

    // Constructor
    public function __construct($json) {
        $validator = new \JsonSchema\Validator();
        $validator->validate($json, (object) array('$ref' => 'file://' . __DIR__ . '/../resources/alphabet_schema.json'));
        if (!$validator->isValid()) {
            throw new InvalidAlphabetException();
        }

        $this->add_symbols($json->alphabets->en);
        $this->code = strtoupper($json->code);
        $this->title = $json->title->en;
        if (isset($json->description)) {
            $this->description = $json->description;
        }
        if (isset($json->source)) {
            $this->source = $json->source;
        }
        $this->case_sensitive = isset($json->case_sensitive) ? $json->case_sensitive : false;
    }

    public function add_symbols($alphabet) {
        foreach ($alphabet as $key => $value) {
            $this->add_symbol($key, $value);
        }
    }

    public function set_case_sensitivity($case) {
        $this->case_sensitive = $case;
    }

    public function add_symbol($symbol, $representation, $ipa = null) {
        if (!$this->case_sensitive) {
            $symbol = strtoupper($symbol);
        }
        $this->alphabet[$symbol] = array(
            $representation, $ipa
        );
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

    public function phonetify($string, $ipa = false) {
        $phonetic = array();
        if (!$ipa) {
            foreach (str_split($string) as $char) {
                $symbol = $this->get_symbol_represenation($char);
                if ($symbol != null) {
                    $phonetic[] = $this->get_symbol_represenation($char);
                }
            }

            return implode(' ', $phonetic);
        }
        $ipa = array();
        foreach (str_split($string) as $char) {
            $symbol = $this->get_symbol_represenation($char, true);
            if ($symbol != null) {
                list($phonet, $pron) = $symbol;
                $phonetic[] = $phonet;
                $ipa[] = $pron . ' ';
            }
        }

        return array(implode(' ', $phonetic), implode(' ', $ipa));
    }
}
