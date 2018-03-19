<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

chdir('../');
include_once './vendor/autoload.php';
include_once './src/Synful/Util/Functions/Sql.php';
include_once './src/Synful/Util/Functions/System.php';
include_once './src/Synful/Util/Functions/Strings.php';
include_once './src/Synful/Util/Functions/Logging.php';


use \Synful\Synful;
use \Synful\Util\IO\IOFunctions;
use \Synful\Util\Data\Models\APIKey;

if (! IOFunctions::loadConfig()) {
    echo "Failed to load config.";
}

Synful::initializeDatabases();

//$apiKey = APIKey::addNew('Nathan Fiscaletti', 'NAFISC2', 0, 10, false, false);

$apiKey = APIKey::getApiKey('NAFISC2');
print_r($apiKey);

$apiKey->delete();

/*
class APIKey extends Illuminate\Database\Eloquent\Model {
    protected $table = 'api_keys';
    protected $connection = 'synful';
}

$apiKey = APIKey::where('auth', '=', 'NAFISC')->get();

print_r($apiKey);
*/