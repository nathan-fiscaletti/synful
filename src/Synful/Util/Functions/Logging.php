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
     * @param  bool   $write_to_file
     * @return mixed
     */
    function sf_info($data, $force = false, $block_header_on_echo = false, $write_to_file = true)
    {
        return \Synful\Util\IO\IOFunctions::out(
            \Synful\Util\IO\LogLevel::INFO,
            $data,
            $force,
            $block_header_on_echo,
            $write_to_file
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
     * @param  bool    $write_to_file
     * @return mixed
     */
    function sf_warn($data, $force = false, $block_header_on_echo = false, $write_to_file = true)
    {
        return \Synful\Util\IO\IOFunctions::out(
            \Synful\Util\IO\LogLevel::WARN,
            $data,
            $force,
            $block_header_on_echo,
            $write_to_file
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
     * @param  bool    $write_to_file
     * @return mixed
     */
    function sf_error($data, $force = false, $block_header_on_echo = false, $write_to_file = true)
    {
        return \Synful\Util\IO\IOFunctions::out(
            \Synful\Util\IO\LogLevel::ERRO,
            $data,
            $force,
            $block_header_on_echo,
            $write_to_file
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
     * @param  bool    $write_to_file
     * @return mixed
     */
    function sf_note($data, $force = false, $block_header_on_echo = false, $write_to_file = true)
    {
        return \Synful\Util\IO\IOFunctions::out(
            \Synful\Util\IO\LogLevel::NOTE,
            $data,
            $force,
            $block_header_on_echo,
            $write_to_file
        );
    }
}

if (! function_exists('sf_input')) {
    function sf_input($data, $level)
    {
        \Synful\Util\IO\IOFunctions::out(
            $level,
            $data,
            true,
            false,
            false
        );
        $out_line = '['.sf_color('SYNFUL', 'white', null, 'reset').'] ';
        $out_line .= \Synful\Util\IO\IOFunctions::parseLogstring(
            \Synful\Util\IO\LogLevel::INFO,
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
     * @return mixed
     */
    function sf_respond($code, $data, $to_file = false)
    {
        http_response_code($code);

        return \Synful\Util\IO\IOFunctions::out(
            \Synful\Util\IO\LogLevel::RESP,
            $data,
            true,
            true,
            $to_file
        );
    }
}
