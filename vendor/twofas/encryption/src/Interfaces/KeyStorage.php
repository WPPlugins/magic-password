<?php

namespace TwoFAS\Encryption\Interfaces;

/**
 * Interface KeyStorage
 * @package TwoFAS\Encryption\Interfaces
 */
interface KeyStorage
{
    /**
     * Store key in key storage so it can be retrieved for future use
     *
     * @param Key $key
     */
    public function storeKey(Key $key);

    /**
     * Retrieve stored Key object.
     *
     * @return Key
     */
    public function retrieveKey();
}
