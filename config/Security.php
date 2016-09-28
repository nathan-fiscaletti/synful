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
     | If enabled, all requests and responses will be encrypted using
     | Synful\Util\Encryption. These functions by default use AES-256 bit
     | encryption however, this is only there as an example. You should not use
     | it in production. You can however place custom code in the Encryption
     | class to handle your own encryption.
     */

    'use_encryption' => false,

    /*
     |--------------------------------------------------------------------------
     | Encryption Key
     |--------------------------------------------------------------------------
     |
     | This key will be loaded into the encryption class to be used with your
     | custom encryption. Must be either 16, 24 or 32 characters long to use
     | AES-256 bit encryption.
     */

    'encryption_key' => 'keymustbe16clong',

];
