<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Allow Public Requests
     |--------------------------------------------------------------------------
     |
     | If set to false, all public request handlers will be treated as
     | private request handlers.
     */

    'allow_public_requests' => true,

    /*
     |--------------------------------------------------------------------------
     | Master Name
     |--------------------------------------------------------------------------
     |
     | The name that will be used when the master key is generated.
     */

    'name' => 'John Doe',

    /*
     |--------------------------------------------------------------------------
     | Master Email
     |--------------------------------------------------------------------------
     |
     | The email that will be used when the master key is generated.
     */

    'email' => 'john@acme.coom',

    /*
     |--------------------------------------------------------------------------
     | Use Encryption
     |--------------------------------------------------------------------------
     |
     | If enabled, all requests and responses must be encrypted using byte
     | shifting and the encryption key. (This is very simple encrpytino, if
     | you want something more secure, please look at another solution).
     */

    'use_encryption' => false,

    /*
     |--------------------------------------------------------------------------
     | Encryption Key
     |--------------------------------------------------------------------------
     |
     | The key to use for encrypting / decrypting requests and responses.
     */

    'encryption_key' => 'mysecretkey',

    /*
     |--------------------------------------------------------------------------
     | Encryption Strength
     |--------------------------------------------------------------------------
     |
     | The strength of the encryption. Between 1 and 120.
     */

    'encryption_strength' => 100,

];
