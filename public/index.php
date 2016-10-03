<?php

/**
 * This file is the public facing index for the framework.
 * All other files are only accessible server side.
 *
 * It is important that this directory is your web root.
 */
chdir('../');

if (file_exists('./vendor')) {
    include './vendor/autoload.php';
    \Synful\Synful::initialize();
} else {
    echo 'Please run \'./synful install\' before running Synful.'."\r\n";
    exit();
}
