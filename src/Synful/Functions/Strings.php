<?php

/*
 |------------------------------------------------------------------------------
 | String Functions
 |------------------------------------------------------------------------------
 |
 | This set of functions is used for manipulating strings.
 */

if (! function_exists('sf_color')) {

    /**
     * Colors a string and returns it.
     *
     * @param  string $string
     * @param  string $foreground_color
     * @param  string $background_color
     * @param  string $reset
     * @return string
     */
    function sf_color($string, $foreground_color = null, $background_color = null, $reset = 'white')
    {
        return \Synful\ASCII\Colors::colorString($string, $foreground_color, $background_color, $reset);
    }
}
