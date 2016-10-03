<?php

/**
 * This file is used for making tests requests to Synful. 
 * In order to use this file, go into /config/System.php and set
 * 'production' to 'false'.
 */

chdir('../');

if (file_exists('./vendor')) {
    include './vendor/autoload.php';
    \Synful\Synful::testForm();    
} else {
    echo 'Please run \'./synful install\' before running Synful.'."\r\n";
    exit();
}    

?>

