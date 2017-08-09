<?php

use TwoFAS\UserZone\OAuth\Interfaces\TokenStorage;
use TwoFAS\UserZone\OAuth\Token;
use TwoFAS\UserZone\OAuth\TokenNotFoundException;

class RandomStorage implements TokenStorage
{
    /**
     * @inheritdoc
     */
    public function storeToken(Token $token) {
        throw new \LogicException();
    }

    /**
     * @inheritdoc
     */
    public function retrieveToken($type) {
        if ($type === 'wordpress') {
            return new Token('wordpress', 'abc.def.abc', 0);
        }

        if ($type === 'setup') {
            return new Token('setup', 'abc.def.abc', 0);
        }

        throw new TokenNotFoundException;
    }
}