<?php

namespace Worthwelle\Alphonic;

use Worthwelle\Alphonic\Alphabet;

class Alphonic {
    
    public $alphabets = array();
    
    // Constructor
    public function __construct() {
        
    }
    
    public function load_alphabets() {
        $files = glob(__DIR__.'/../alphabets/*.json');
        foreach( $files as $file ) {
            $this->add_alphabet_from_file($file);
        }
    }
    
    public function add_alphabet_from_json($json) {
        $alpha = new Alphabet(json_decode($json, true));
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
