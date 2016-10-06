<?php

namespace Synful\Util\Security;

use Synful\Util\Framework\Object;

/**
 * Class used for encrypting requests and responses.
 */
class Encryption
{
    use Object;

    /**
     * The encryption key that is automatically loaded from the configuration.
     *
     * @var string
     */
    private $key;

    /**
     * Handle encrypting data.
     *
     * @param  string $data
     * @return string
     */
    public function encrypt($data)
    {
        // Place custom code here.
        // Note: The Aes class is only meant to be an example for encryption.
        //       Do not use it in production.
        return Aes256BitEncryption::encrypt($data, $this->key);
    }

    /**
     * Handle decrypting data.
     *
     * @param  string $data
     * @return string
     */
    public function decrypt($data)
    {
        // Place custom code here.
        // Note: The Aes class is only meant to be an example for encryption.
        //       Do not use it in production.
        return Aes256BitEncryption::decrypt($data, $this->key);
    }
}
