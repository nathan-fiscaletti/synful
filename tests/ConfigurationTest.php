<?php

namespace Synful\Tests;

use PHPUnit\Framework\TestCase;
use Synful\IO\IOFunctions;

final class ConfigurationTest extends TestCase {
    /**
     * Verify that all of the built in global functions are loaded
     * when Synful::loadGlobalFunctions() is invoked.
     */
    public function testLoadConfig() {
        $result = IOFunctions::loadConfig();
        $this->assertTrue($result);
    }
}