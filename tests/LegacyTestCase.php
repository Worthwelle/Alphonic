<?php

namespace Tests;

use org\bovigo\vfs\vfsStream;

abstract class LegacyTestCase extends BaseTestCase {
    /**
     * @var vfsStreamDirectory
     */
    public $root;

    /**
     * Normalize expectException function between PHPUnit 4 and PHPUnit 5
     */
    public function expectException($class) {
        parent::setExpectedException($class);
    }

    /**
     * Set up test environmemt
     */
    public function setUp() {
        $this->root = vfsStream::setup('root', null, $this->structure);
    }
}
