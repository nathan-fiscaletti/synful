<?php

/*
 |------------------------------------------------------------------------------
 | Logging Abstraction Functions
 |------------------------------------------------------------------------------
 |
 | This set of functions is used to abstract the calls 'to IOFunctions::out'.
 */

if (! function_exists('sf_info')) {

    /**
     * Prints output to the console and logs with INFO log type.
     *
     * @param  string $data
     * @param  bool   $force
     * @param  bool   $block_header_on_echo
     * @return mixed
     */
    function sf_info($data, $force = false, $block_header_on_echo = false)
    {
        return \Synful\IO\IOFunctions::out(
            \Synful\IO\LogLevel::INFO,
            $data,
            $force,
            $block_header_on_echo
        );
    }
}

if (! function_exists('sf_warn')) {

    /**
     * Prints output to the console and logs with WARN log type.
     *
     * @param  string  $data
     * @param  bool    $force
     * @param  bool    $block_header_on_echo
     * @return mixed
     */
    function sf_warn($data, $force = false, $block_header_on_echo = false)
    {
        return \Synful\IO\IOFunctions::out(
            \Synful\IO\LogLevel::WARN,
            $data,
            $force,
            $block_header_on_echo
        );
    }
}

if (! function_exists('sf_error')) {

    /**
     * Prints output to the console and logs with ERRO log type.
     *
     * @param  string  $data
     * @param  bool    $force
     * @param  bool    $block_header_on_echo
     * @return mixed
     */
    function sf_error($data, $force = false, $block_header_on_echo = false)
    {
        return \Synful\IO\IOFunctions::out(
            \Synful\IO\LogLevel::ERRO,
            $data,
            $force,
            $block_header_on_echo
        );
    }
}

if (! function_exists('sf_note')) {

    /**
     * Prints output to the console and logs with NOTE log type.
     *
     * @param  string  $data
     * @param  bool    $force
     * @param  bool    $block_header_on_echo
     * @return mixed
     */
    function sf_note($data, $force = false, $block_header_on_echo = false)
    {
        return \Synful\IO\IOFunctions::out(
            \Synful\IO\LogLevel::NOTE,
            $data,
            $force,
            $block_header_on_echo
        );
    }
}

if (! function_exists('sf_input')) {

    /**
     * Retrieves input from the user with the specified prompt.
     *
     * @param  string $prompt
     * @param  int    $level
     * @return string
     */
    function sf_input($prompt, $level)
    {
        \Synful\IO\IOFunctions::out(
            $level,
            $prompt,
            true,
            false
        );
        $out_line = '['.sf_color('SYNFUL', 'white', null, 'reset').'] ';
        $out_line .= \Synful\IO\IOFunctions::parseLogstring(
            $level,
            'INFO',
            '> '
        );

        return readline(
            $out_line
        );
    }
}

if (! function_exists('sf_respond')) {

    /**
     * Prints output to the console and logs with RESP log type.
     *
     * @param  int     $code
     * @param  string  $data
     * @param  array   $headers
     *
     * @return mixed
     */
    function sf_respond($code, $data, $headers = [])
    {
        http_response_code($code);

        foreach ($headers as $key => $value) {
            header($key.': '.$value);
        }

        return \Synful\IO\IOFunctions::out(
            \Synful\IO\LogLevel::RESP,
            $data,
            true,
            true
        );
    }
}
