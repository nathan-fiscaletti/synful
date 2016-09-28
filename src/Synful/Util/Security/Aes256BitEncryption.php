<?php

namespace Synful\Util\Security;

/**
 * Example AES-256 Bit Encryption
 * You must have mcrypt enabled in your php.ini file to use this class.
 */
class Aes256BitEncryption
{
    /**
     * Encrypt data using AES-256 bit encryption based on a key.
     *
     * @param  string $data
     * @param  string $key
     * @return string
     */
    public static function encrypt($data, $key)
    {
        return trim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    $key,
                    $data,
                    MCRYPT_MODE_ECB,
                    mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_256,
                            MCRYPT_MODE_ECB
                        ),
                        MCRYPT_RAND
                    )
                )
            )
        );
    }

    /**
     * Decrypt data using AES-256 bit encryption based on a key.
     *
     * @param  string $data
     * @param  string $key
     * @return string
     */
    public static function decrypt($data, $key)
    {
        return trim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                $key,
                base64_decode(
                    $data
                ),
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                    ),
                    MCRYPT_RAND
                )
            )
        );
    }
}
