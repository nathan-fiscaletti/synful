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
     * @param  string  $data
     * @param  boolean $force
     * @param  boolean $block_header_on_echo
     * @param  boolean $write_to_file
     * @return mixed
     */
    function sf_info($data, $force = false, $block_header_on_echo = false, $write_to_file = true)
    {
        return \Synful\IO\IOFunctions::out(
            \Synful\IO\LogLevel::INFO,
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
     * @param  boolean $force
     * @param  boolean $block_header_on_echo
     * @param  boolean $write_to_file
     * @return mixed
     */
    function sf_warn($data, $force = false, $block_header_on_echo = false, $write_to_file = true)
    {
        return \Synful\IO\IOFunctions::out(
            \Synful\IO\LogLevel::WARN,
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
     * @param  boolean $force
     * @param  boolean $block_header_on_echo
     * @param  boolean $write_to_file
     * @return mixed
     */
    function sf_error($data, $force = false, $block_header_on_echo = false, $write_to_file = true)
    {
        return \Synful\IO\IOFunctions::out(
            \Synful\IO\LogLevel::ERRO,
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
     * @param  boolean $force
     * @param  boolean $block_header_on_echo
     * @param  boolean $write_to_file
     * @return mixed
     */
    function sf_note($data, $force = false, $block_header_on_echo = false, $write_to_file = true)
    {
        return \Synful\IO\IOFunctions::out(
            \Synful\IO\LogLevel::NOTE,
            $data,
            $force,
            $block_header_on_echo,
            $write_to_file
        );
    }

}

if (! function_exists('sf_respond')) {

    /**
     * Prints output to the console and logs with RESP log type.
     *
     * @param  string  $data
     * @return mixed
     */
    function sf_respond($data)
    {
        return \Synful\IO\IOFunctions::out(
            \Synful\IO\LogLevel::RESP,
            $data,
            true,
            true,
            false
        );
    }

}
