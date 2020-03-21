<?php /** @noinspection PhpUnhandledExceptionInspection */

/**
 * This file is the public facing index for the framework.
 * All other files are only accessible server side.
 *
 * It is important that this directory is your web root.
 */

use Synful\Synful;

chdir('../');

if (file_exists('./vendor')) {
    include './vendor/autoload.php';
    Synful::initialize();
} else {
    echo 'Please run \'./synful install\' before using Synful.'."\r\n";
    exit();
}
