# Alphonic

[![Build Status](https://travis-ci.org/worthwelle/alphonic.svg?branch=master)](https://travis-ci.org/worthwelle/alphonic)
[![Latest Stable Version](https://poser.pugx.org/worthwelle/alphonic/v/stable.png)](https://packagist.org/packages/worthwelle/alphonic)
[![Total Downloads](https://poser.pugx.org/worthwelle/alphonic/downloads.png)](https://packagist.org/packages/worthwelle/alphonic)

A library for converting strings into and from phonetic spellings for use over the phone, a radio or other unclear communications channels. Alphonic provides these conversions not only using the standard NATO alphabet, but also various other phonetic alphabets, making it perfect for easily converting strings with historical accuracy.

**Important note**

The alphabets in this library are provided not only as-is, but also as-were. That is, these alphabets are provided as they were originally defined. This means that the characters supported by each alphabet differ. Some alphabets cover only the [classical Latin alphabet](https://en.wikipedia.org/wiki/Latin_alphabet#Classical_Latin_alphabet), some include numbers, some include language-specific Latin characters (such as the various letters with [diacritical](https://en.wikipedia.org/wiki/Diacritic) marks), and some include punctuation and figure-specific characters.

These limitations can be overcome using the `add_symbol` function, which allows adding characters to the a particular alphabet on a one-time-only basis. A fill-in function will be provided in a future version of the library that will allow adding a list of characters only if the characters don't already exist in the alphabet. This will, naturally, be a non-standard use of the alphabet

More information on creating custom alphabets will be available in the near future.

## Installation

### Library

```bash
git clone https://github.com/worthwelle/alphonic.git
```

### Composer

[Install PHP Composer](https://getcomposer.org/doc/00-intro.md)

```bash
composer require worthwelle/alphonic
```

## Usage

### Load pre-configured alphabets

```php
<?php

$alphonic = new Alphonic();
$alphonic->load_alphabets();
$alphonic->alphabet("NATO")->add_symbol(" ","|");
$alphonic->alphabet("NATO")->add_symbol(",","Comma");
$alphonic->alphabet("NATO")->add_symbol("!","Exclamation");

echo $output = $alphonic->phonetify("Holy alphabets, Batman!", "NATO");
// Hotel Oscar Lima Yankee | Alfa Lima Papa Hotel Alfa Bravo Echo Tango Sierra Comma | Bravo Alfa Tango Mike Alfa November Exclamation
echo $alphonic->unphonetify($output, $alpha);
// HOLY ALPHABETS, BATMAN!
```

### Load a custom alphabet

```php
<?php

$alphonic = new Alphonic();
$alphonic->load_alphabet_from_file('myalphabet.json');
$alphonic->load_alphabet_from_json(file_get_contents('myotheralphabet.json');

// The second argument allows Alphonic to skip alphabets that don't pass validation.
// This defaults to false.
$alphonic2 = new Alphonic('directory/full/of/alphabets', true);

```

### Configuration Options



## Running the tests

```bash
composer test                            # run all unit tests
composer testOnly TestClass              # run specific unit test class
composer testOnly TestClass::testMethod  # run specific unit test method
composer style-check                     # check code style for errors
composer style-fix                       # automatically fix code style errors
```

## Contributing

## Alphabets

Currently supported alphabets:

 * \[`NATO`\] NATO phonetic alphabet (2008 respelling)
    * AKA: International Radiotelephony Spelling Alphabet
    * AKA: ICAO phonetic alphabet
 * \[`UECU1920`\] Universal Electrical Communications Union (1920)
 * \[`CCIR1927`\] International Radiotelegraph Convention (1927)
 * \[`CCIR-ICAN1932`\] General Radiocommunication and Additional Regulations (1932)
 * \[`IRCC1938`\] International Radiocommunication Conference (Cairo, 1938)