<?php

namespace TwoFAS\Encryption;

use TwoFAS\Encryption\Interfaces\Key;

/**
 * Class AESGeneratedKey implementing Key interface.
 * @package TwoFAS\Encryption
 */
class AESGeneratedKey implements Key
{
    /**
     * String representing AESKey
     *
     * @var string
     */
    private $key;

    /**
     * Key length
     *
     * @var int
     */
    private $keyLength = 128;

    /**
     * AESGeneratedKey constructor.
     */
    public function __construct()
    {
        $this->key = openssl_random_pseudo_bytes($this->keyLength);
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->key;
    }
}