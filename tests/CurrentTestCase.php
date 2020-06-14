<?php

namespace Tests;

use org\bovigo\vfs\vfsStream;

abstract class CurrentTestCase extends BaseTestCase {
    /**
     * @var vfsStreamDirectory
     */
    public $root;

    /**
     * Set up test environmemt
     */
    public function setUp(): void {
        $this->root = vfsStream::setup('root', null, $this->structure);
    }
}
