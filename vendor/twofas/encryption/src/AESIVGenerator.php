<?php

namespace TwoFAS\Encryption;

use TwoFAS\Encryption\Interfaces\IVGenerator;

/**
 * Class AESIVGenerator
 * @package TwoFAS\Encryption
 */
class AESIVGenerator implements IVGenerator
{
    /**
     * @var string
     */
    private $cipherMethod = "AES-256-CBC";

    /**
     * Generate IV to be used in AES-256-CBC algorithm.
     *
     * @return string
     */
    public function getIV()
    {
        return openssl_random_pseudo_bytes($this->getIVLength());
    }

    /**
     * Return length of generated IV.
     *
     * @return int
     */
    public function getIVLength()
    {
        return openssl_cipher_iv_length($this->cipherMethod);
    }

    /**
     * Return AES-256-CBC string.
     *
     * @return string
     */
    public function getCipherMethod()
    {
        return $this->cipherMethod;
    }
}
