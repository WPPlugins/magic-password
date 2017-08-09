<?php

use TwoFAS\UserZone\HttpCodes;
use TwoFAS\UserZone\Response\ResponseGenerator;

class KeysTest extends UserZoneBase
{
    public function testCreateKeyReturnsProductionKeyIfThereIsNoError()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode(array(
                'token' => 'key_token'
            ));

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::CREATED));
        }

        $key = $userZone->createKey(getenv('integration_id'), 'key name');

        $this->assertInstanceOf('\TwoFAS\UserZone\Key', $key);
    }
}