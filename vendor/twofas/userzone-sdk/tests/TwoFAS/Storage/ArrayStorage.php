<?php

use TwoFAS\UserZone\OAuth\Interfaces\TokenStorage;
use TwoFAS\UserZone\OAuth\Token;
use TwoFAS\UserZone\OAuth\TokenNotFoundException;

class ArrayStorage implements TokenStorage
{
    /**
     * @var array
     */
    private $tokens;

    /**
     * EmptyStorage constructor.
     */
    public function __construct()
    {
        $this->tokens = array();
    }

    /**
     * @inheritdoc
     */
    public function storeToken(Token $token) {
        $this->tokens[$token->getType()] = $token;
    }

    /**
     * @inheritdoc
     */
    public function retrieveToken($type) {
        if (array_key_exists($type, $this->tokens)) {
            return $this->tokens[$type];
        }

        throw new TokenNotFoundException;
    }
}