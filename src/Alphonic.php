<?php

namespace Worthwelle\Alphonic;

use Worthwelle\Alphonic\Exception\AlphabetNotFoundException;
use Worthwelle\Alphonic\Exception\InvalidAlphabetException;

class Alphonic {
    public $alphabets = array();

    public function load_alphabets($directories = __DIR__ . '/../alphabets', $skip_invalid = false) {
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

    public function add_alphabet_from_object($alpha) {
        $this->alphabets[$alpha->code] = $alpha;
    }

    public function add_alphabet_from_json($json) {
        $this->add_alphabet_from_object(Alphabet::from_json($json));
    }

    public function add_alphabet_from_file($filename) {
        $alpha = Alphabet::from_file($filename);
        $this->alphabets[$alpha->code] = $alpha;
    }

    public function get_code($alpha) {
        return $this->alphabet($alpha)->get_code();
    }

    public function get_title($alpha) {
        return $this->alphabet($alpha)->get_title();
    }

    public function get_description($alpha) {
        return $this->alphabet($alpha)->get_description();
    }

    public function get_source($alpha) {
        return $this->alphabet($alpha)->get_source();
    }

    public function get_alphabets() {
        return $this->alphabets;
    }

    public function phonetify($string, $alpha, $return_missing = false) {
        return $this->alphabet($alpha)->phonetify($string, $return_missing);
    }

    public function unphonetify($string, $alpha, $return_missing = false) {
        return $this->alphabet($alpha)->unphonetify($string, $return_missing);
    }

    public function &alphabet($alpha) {
        $alpha = strtoupper($alpha);
        if (!isset($this->alphabets[$alpha])) {
            throw new AlphabetNotFoundException();
        }

        return $this->alphabets[$alpha];
    }
}
