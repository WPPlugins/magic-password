<?php

namespace TwoFAS\UserZone\OAuth\Interfaces;

use TwoFAS\UserZone\OAuth\Token;
use TwoFAS\UserZone\OAuth\TokenNotFoundException;

interface TokenStorage
{
    /**
     * Store token in storage so it can be retrieved for future use
     *
     * @param Token $token
     */
    public function storeToken(Token $token);

    /**
     * Retrieve stored Token object.
     *
     * @param string $type
     *
     * @return Token
     *
     * @throws TokenNotFoundException
     */
    public function retrieveToken($type);
}
