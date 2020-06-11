<?php

namespace Tests;

if (version_compare(phpversion(), '7.1', '<') && version_compare(\PHPUnit_Runner_Version::id(), '8', '<')) {
    abstract class TestCase extends LegacyTestCase {
    }
} elseif (method_exists('\PHPUnit_Runner_Version', 'id') && version_compare(\PHPUnit_Runner_Version::id(), '5.2', '<')) {
    abstract class TestCase extends LegacyTestCase {
    }
} else {
    abstract class TestCase extends CurrentTestCase {
    }
}
