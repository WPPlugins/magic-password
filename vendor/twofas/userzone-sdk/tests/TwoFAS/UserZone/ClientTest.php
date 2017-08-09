<?php

use TwoFAS\UserZone\HttpCodes;
use TwoFAS\UserZone\Response\ResponseGenerator;

class ClientTest extends UserZoneBase
{
    public function testGetClient()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        $clientId = getenv('client_id');
        $email    = getenv('client_email');
        $cardId   = getenv('card_id');

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode(array(
                'id'                     => $clientId,
                'email'                  => $email,
                'has_card'               => true,
                'has_generated_password' => true,
                'primary_card_id'        => $cardId
            ));

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::OK));
        }

        $client = $userZone->getClient();

        $this->assertInstanceOf('\TwoFAS\UserZone\Client', $client);

        $this->assertEquals($clientId, $client->getId());
        $this->assertEquals($email, $client->getEmail());
        $this->assertTrue($client->hasCard());
        $this->assertTrue($client->hasGeneratedPassword());
        $this->assertEquals($cardId, $client->getPrimaryCardId());
    }

    public function testCreateClient()
    {
        $userZone   = $this->getEmptyUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        $email    = 'example@2fas.com';
        $password = 'simple';

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode(array(
                'id'                     => 1,
                'email'                  => $email,
                'has_card'               => false,
                'has_generated_password' => true,
                'primary_card_id'        => null
            ));

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::CREATED));
        }

        $client = $userZone->createClient($email, $password, $password, 'wordpress');

        $this->assertInstanceOf('\TwoFAS\UserZone\Client', $client);
    }

    public function testCreateClientThrowsValidationExceptionIfDataIsInvalid()
    {
        $userZone   = $this->getEmptyUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = $this->getExpectedValidationBody(
                array(
                    'email' => array('validation.required')
                )
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::BAD_REQUEST));
        }

        $this->setExpectedException('\TwoFAS\UserZone\Exception\ValidationException');

        $userZone->createClient('', 'simple', 'simple', 'wordpress');
    }
}