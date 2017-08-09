<?php

use TwoFAS\UserZone\Errors;
use TwoFAS\UserZone\HttpCodes;
use TwoFAS\UserZone\Response\ResponseGenerator;

class AuthorisationExceptionsTest extends UserZoneBase
{
    public function testCreateIntegrationWithoutKey()
    {
        $userZone   = $this->getEmptyUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        $this->setExpectedException('\TwoFAS\UserZone\OAuth\TokenNotFoundException', '');

        $userZone->createIntegration('Unauthorized integration');
    }

    public function testCallMethodWhichRequiresAuthenticationWithRandomKey()
    {
        $userZone   = $this->getUserZoneWithRandomKeys();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = array(
                'error' => array(
                    'code' => Errors::UNAUTHORIZED,
                    'msg'  => 'Unauthorized'
                )
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::UNAUTHORIZED));
        }

        $this->setExpectedException('\TwoFAS\UserZone\Exception\AuthorizationException', 'Unauthorized');

        $userZone->createIntegration('Unauthorized integration');
    }

    public function testCallMethodWhichRequiresAuthenticationWithRevokedKey()
    {
        $userZone   = $this->getUserZoneWithRevokedKeys();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = array(
                'error' => array(
                    'code' => Errors::UNAUTHORIZED,
                    'msg'  => 'Unauthorized'
                )
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::UNAUTHORIZED));
        }

        $this->setExpectedException('\TwoFAS\UserZone\Exception\AuthorizationException', 'Unauthorized');

        $userZone->createIntegration('Unauthorized integration');
    }
}