<?php

namespace TwoFAS\Encryption;

use TwoFAS\Encryption\Exceptions\RsaDecryptException;
use TwoFAS\Encryption\Exceptions\RsaEncryptException;

class RsaCryptographer
{
    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @param string $publicKey
     * @param string $privateKey
     */
    public function __construct($publicKey, $privateKey)
    {
        $this->publicKey  = base64_decode($publicKey, true);
        $this->privateKey = base64_decode($privateKey, true);
    }

    /**
     * @param string $data
     *
     * @return string
     *
     * @throws RsaEncryptException
     */
    public function encryptToBase64($data)
    {
        if (openssl_public_encrypt($data, $encrypted, $this->publicKey)) {
            return base64_encode($encrypted);
        }

        throw new RsaEncryptException('Encrypting went wrong');
    }

    /**
     * @param string $data
     *
     * @return string
     *
     * @throws RsaDecryptException
     */
    public function decryptBase64($data)
    {
        if (openssl_private_decrypt(base64_decode($data, true), $decrypted, $this->privateKey)) {
            return $decrypted;
        }

        throw new RsaDecryptException('Decrypting went wrong');
    }
}