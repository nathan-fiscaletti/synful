<?php

/**
 * Synful API Framework.
 * @author  Nathan Fiscaletti <nathan.fiscaletti@gmail.com>
 *
 * Required            : [ PHP 7+, PHP-MySql Extension ]
 * Optionally Required : [ PHP Sockets, PECL PThreads, MySQL Server ]
 *
 *  Synful API is a framework that allows you to create your own API's simply and quickly.
 *  It supports running in two modes, 'standalone' and 'HTTP'.
 *
 *  To run it in standalone, run 'php synful.php standalone=true'
 *      You can configure the standalone settings in 'config.ini'
 *      under the 'system' section or via the command line.
 *      (Standalone mode requires the PHP Sockets Module to be enabled)
 *
 *  To run it in HTTP mode, simply put the files on a web server and
 *  configure the SQL information in 'config.ini'
 *
 * - Multi Thread Support -
 *      Sunful API does fully support multiple requests when running in standalone mode
 *      via multithread capabilities.
 *
 *      (PHP needs the PECL PThreads module enabled in order for this feature to work)
 *
 *      To enable the multithread feature when running in standalone mode,
 *      simply enable it in 'config.ini' under the 'system' section.
 *
 * - Making A Request -
 *      Requests are to be made in JSON format.
 *
 *
 * Request Examples: https://gist.github.com/nathan-fiscaletti/16339c4c1e2f4e8183f3237f2d42d901
 */
if (file_exists('./vendor')) {
    include './vendor/autoload.php';
    \Synful\Synful::initialize();
} else {
    echo 'Please run \'composer install --no-scripts\' before running Synful.'."\r\n";
    exit(3);
}
