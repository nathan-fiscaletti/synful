<?php

namespace Synful\Tests;

use PHPUnit\Framework\TestCase;
use Synful\Synful;

final class FunctionLibraryTest extends TestCase {
    /**
     * Verify that all of the built in global functions are loaded
     * when Synful::loadGlobalFunctions() is invoked.
     */
    public function testDefaultGlobalFunctions() {
        Synful::loadGlobalFunctions();

        // System Functions
        $sf_init_exists = function_exists("sf_init");
        $this->assertTrue($sf_init_exists);
        $sf_conf_exists = function_exists("sf_conf");
        $this->assertTrue($sf_conf_exists);
        $sf_response_exists = function_exists("sf_response");
        $this->assertTrue($sf_response_exists);
        $sf_headers_exists = function_exists("sf_headers");
        $this->assertTrue($sf_headers_exists);
        $sf_color_exists = function_exists("sf_color");
        $this->assertTrue($sf_color_exists);
        $sf_json_decode_exists = function_exists("sf_json_decode");
        $this->assertTrue($sf_json_decode_exists);
        $sf_is_json_exists = function_exists("sf_is_json");
        $this->assertTrue($sf_is_json_exists);
        $sf_respond_exists = function_exists("sf_respond");
        $this->assertTrue($sf_respond_exists);
        $sf_input_exists = function_exists("sf_input");
        $this->assertTrue($sf_input_exists);

        // Logging Functions
        $sf_note_exists = function_exists("sf_note");
        $this->assertTrue($sf_note_exists);
        $sf_error_exists = function_exists("sf_error");
        $this->assertTrue($sf_error_exists);
        $sf_warn_exists = function_exists("sf_warn");
        $this->assertTrue($sf_warn_exists);
        $sf_info_exists = function_exists("sf_info");
        $this->assertTrue($sf_info_exists);
    }
}