<?php

namespace Worthwelle\Alphonic;

use Worthwelle\Alphonic\Alphabet;

class Alphonic {
    
    public $alphabets = array();
    
    // Constructor
    public function __construct() {
        
    }
    
    public function load_alphabets($directories = array(__DIR__.'/../alphabets'), $skip_invalid = false) {
        foreach( $directories as $dir ) {
            $files = glob("$dir/*.json");
            foreach( $files as $file ) {
                try {
                    $this->add_alphabet_from_file($file);
                } catch( \Worthwelle\Alphonic\Exception\InvalidAlphabetException $e ) {
                    if( !$skip_invalid ) throw $e;
                }
            }
        }
    }
    
    public function add_alphabet_from_json($json, $suppress_exceptions = false) {
        $alpha = new Alphabet(json_decode($json));
        $this->alphabets[strtoupper($alpha->code)] = $alpha;
    }
    
    public function add_alphabet_from_file($filename) {
        $this->add_alphabet_from_json(file_get_contents($filename));
    }
    
    public function get_title($alpha) {
        $alpha = strtoupper($alpha);
        if( !isset($this->alphabets[$alpha]) ) throw new \Worthwelle\Alphonic\Exception\AlphabetNotFoundException();
        return $this->alphabets[$alpha]->get_title();
    }
    
    public function get_description($alpha) {
        $alpha = strtoupper($alpha);
        if( !isset($this->alphabets[$alpha]) ) throw new \Worthwelle\Alphonic\Exception\AlphabetNotFoundException();
        return $this->alphabets[$alpha]->get_description();
    }
    
    public function get_source($alpha) {
        $alpha = strtoupper($alpha);
        if( !isset($this->alphabets[$alpha]) ) throw new \Worthwelle\Alphonic\Exception\AlphabetNotFoundException();
        return $this->alphabets[$alpha]->get_source();
    }
    
    public function get_string($string, $alpha, $ipa = false) {
        $alpha = strtoupper($alpha);
        if( !isset($this->alphabets[$alpha]) ) throw new \Worthwelle\Alphonic\Exception\AlphabetNotFoundException();
        return $this->alphabets[$alpha]->get_string($string, $ipa);
    }
    
    public function &alphabet($alpha) {
        $alpha = strtoupper($alpha);
        if( !isset($this->alphabets[$alpha]) ) throw new \Worthwelle\Alphonic\Exception\AlphabetNotFoundException();
        return $this->alphabets[$alpha];
    }
}

?>
