<?php

namespace Worthwelle\Alphonic;

use Worthwelle\Alphonic\Exception\AlphabetNotFoundException;
use Worthwelle\Alphonic\Exception\InvalidAlphabetException;

class Alphonic {
    public $alphabets = array();

    // Constructor
    public function __construct() {
    }

    public function load_alphabets($directories = array(__DIR__ . '/../alphabets'), $skip_invalid = false) {
        foreach ($directories as $dir) {
            $files = glob("$dir/*.json");
            foreach ($files as $file) {
                try {
                    $this->add_alphabet_from_file($file);
                } catch (InvalidAlphabetException $e) {
                    if (!$skip_invalid) {
                        throw $e;
                    }
                }
            }
        }
    }

    public function add_alphabet_from_json($json) {
        $alpha = new Alphabet(json_decode($json));
        $this->alphabets[strtoupper($alpha->code)] = $alpha;
    }

    public function add_alphabet_from_file($filename) {
        $this->add_alphabet_from_json(file_get_contents($filename));
    }

    public function get_title($alpha) {
        $alpha = strtoupper($alpha);
        if (!isset($this->alphabets[$alpha])) {
            throw new AlphabetNotFoundException();
        }

        return $this->alphabets[$alpha]->get_title();
    }

    public function get_description($alpha) {
        $alpha = strtoupper($alpha);
        if (!isset($this->alphabets[$alpha])) {
            throw new AlphabetNotFoundException();
        }

        return $this->alphabets[$alpha]->get_description();
    }

    public function get_source($alpha) {
        $alpha = strtoupper($alpha);
        if (!isset($this->alphabets[$alpha])) {
            throw new AlphabetNotFoundException();
        }

        return $this->alphabets[$alpha]->get_source();
    }

    public function phonetify($string, $alpha, $ipa = false) {
        $alpha = strtoupper($alpha);
        if (!isset($this->alphabets[$alpha])) {
            throw new AlphabetNotFoundException();
        }

        return $this->alphabets[$alpha]->phonetify($string, $ipa);
    }

    public function unphonetify($string, $alpha) {
        $alpha = strtoupper($alpha);
        if (!isset($this->alphabets[$alpha])) {
            throw new AlphabetNotFoundException();
        }

        return $this->alphabets[$alpha]->unphonetify($string);
    }

    public function &alphabet($alpha) {
        $alpha = strtoupper($alpha);
        if (!isset($this->alphabets[$alpha])) {
            throw new AlphabetNotFoundException();
        }

        return $this->alphabets[$alpha];
    }
}
