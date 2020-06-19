# Alphonic

[![Build Status](https://travis-ci.org/worthwelle/alphonic.svg?branch=master)](https://travis-ci.org/worthwelle/alphonic)
[![Latest Stable Version](https://poser.pugx.org/worthwelle/alphonic/v/stable.png)](https://packagist.org/packages/worthwelle/alphonic)
[![Total Downloads](https://poser.pugx.org/worthwelle/alphonic/downloads.png)](https://packagist.org/packages/worthwelle/alphonic)

A library for converting strings into and from phonetic spellings for use over the phone, a radio or other unclear communications channels. Alphonic provides these conversions not only using the standard NATO alphabet, but also various other phonetic alphabets, making it perfect for easily converting strings with historical accuracy.

**Important note**

The alphabets in this library are provided not only as-is, but also as-were. That is, these alphabets are provided as they were originally defined. This means that the characters supported by each alphabet differ. Some alphabets cover only the [classical Latin alphabet](https://en.wikipedia.org/wiki/Latin_alphabet#Classical_Latin_alphabet), some include numbers, some include language-specific Latin characters (such as the various letters with [diacritical](https://en.wikipedia.org/wiki/Diacritic) marks), and some include punctuation and figure-specific characters.

These limitations can be overcome using the `add_symbol` function, which allows adding characters to a particular alphabet on a one-time-only basis. To fill in only missing symbols in multiple alphabets, the `add_symbol` or `add_symbols` functions can be used with `$overwrite` set to false. This will, naturally, be a non-standard use of the alphabet.

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

echo $output = $alphonic->phonetify("Holy alphabets, Batman!", "Alphonic-NATO");
// Hotel Oscar Lima Yankee | Alfa Lima Papa Hotel Alfa Bravo Echo Tango Sierra
// Comma | Bravo Alfa Tango Mike Alfa November Exclamation

echo $alphonic->unphonetify($output, "Alphonic-NATO");
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

### Use localized Alphabets

Alphonic supports alphabets with multiple locales, such as that defined in the [General Radiotelegraph Regulations][ccir1927] of 1927, which is defined with localized spellings for both English and French.

```php
<?php

$alphonic = new Alphonic();
$alphonic->load_alphabets();

echo $fr = $alphonic->phonetify("Holy alphabets, Batman!", "Alphonic-CCIR1927", "fr");
// Hanovre Ontario Liverpool Yokohama Amsterdam Liverpool Portugal Hanovre
// Amsterdam Baltimore Eddiston Tokio Santiago Baltimore Amsterdam Tokio
// Madagascar Amsterdam Neuchâtel

echo $en = $alphonic->phonetify("Holy alphabets, Batman!", "Alphonic-CCIR1927", "en");
// Hanover Ontario Liverpool Yokohama Amsterdam Liverpool Portugal Hanover
// Amsterdam Baltimore Eddystone Tokio Santiago Baltimore Amsterdam Tokio
// Madagascar Amsterdam Neufchatel

echo $alphonic->unphonetify($fr, "Alphonic-CCIR1927", "fr");
echo $alphonic->unphonetify($en, "Alphonic-CCIR1927", "en");
// HOLYALPHABETSBATMAN
```

## Known Issues

* During early development the JSON schema is likely to change without notice. Once v1.0 is formalized, this will become the standard going forward.

## Running the tests

```bash
composer test                            # run all unit tests
composer test-timing                     # report the runtime of each test into an XML file
composer coverage                        # run unit tests and report code coverage
composer coverage-report                 # run unit tests and provide a thorough code coverage report
composer testOnly TestClass              # run specific unit test class
composer testOnly TestClass::testMethod  # run specific unit test method
composer style-check                     # check code style for errors
composer style-fix                       # automatically fix code style errors
```

## Contributing

## Alphabets

Currently supported alphabets:

| Code | Alphabet Title |
|------|----------------|
| `APCO1941` | Association of Public-Safety Communications Officials-International (1941) |
| `APCO1967` | Association of Public-Safety Communications Officials-International Project 2 (1967) |
| `APCO1974` | Association of Public-Safety Communications Officials-International Project 14 (1974) |
| `ARRL1936` | American Radio Relay League (1936) |
| `CCBUSUK1943` | Combined Communications Board (1943) |
| `CCIR-ICAN1932` | General Radiocommunication and Additional Regulations (CCIR/ICAN, 1932) |
| `CCIR1927` | General Radiotelegraph Regulations (1927) |
| `ICAO1946` | ICAO Second Session of the Communications Division (1946) |
| `IRC1947` | Radio Regulations and Additional Radio Regulations (Atlantic City, 1947) |
| `IRCC1938` | International Radiocommunication Conference (Cairo, 1938) |
| `LVMPD` | Las Vegas Metropolitan Police Department |
| `NATO` | NATO Phonetic Alphabet |
| `NATO1956` | NATO Phonetic Alphabet (Jan 1 - Feb 29, 1956) |
| `UECU1920` | Universal Electrical Communications Union (1920) |
| `USAFM2412-1943` | U.S. Army Field Manual 24-12 (1943-1955) |
| `USAFM245-1939` | U.S. Army Field Manual 24-5 (1939-1943) |
| `USAFM245-1941` | U.S. Army Field Manual 24-5 (1941-1943) |
| `USAS1919` | U.S. Air Service (1919) |
| `USASB1916` | U.S. Army Signal Book (1916-1939) |
| `USJAN1941` | U.S. Joint Army/Navy (1941-1943) |
| `USJAN1941A` | U.S. Joint Army/Navy variant (1941-1943) |
| `USN1908-1` | U.S. Navy (1908; Version 1) |
| `USN1908-2` | U.S. Navy (1908; Version 2) |
| `USN1913` | U.S. Navy (1913-1926) |
| `USN1927` | U.S. Navy (1927-1937) |
| `USN1938` | U.S. Navy (1938) |
| `USNWW2` | U.S. Navy World War II |
| `WU1918` | Western Union (1918) |

[ccir1927]: alphabets/ccir1927.json
