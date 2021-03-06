<?php

use TwoFAS\UserZone\HttpCodes;
use TwoFAS\UserZone\Integration;
use TwoFAS\UserZone\Response\ResponseGenerator;
use TwoFAS\UserZone\OAuth\Token;
use TwoFAS\UserZone\OAuth\TokenType;

class IntegrationsTest extends UserZoneBase
{
    public function testCreateIntegrationReturnsIntegrationIfThereIsNoError()
    {
        // setup scope
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        $name       = 'Integration name';
        $privateKey = 'MIGqAgEAAiEApxS+t3N6TzgEZPW0vk3RIJqSeeYCa5ThY8be10GzPwsCAwEAAQIgdvVROIJSkfabQlqiXmA';
        $publicKey  = 'MDwwDQYJKoZIhvcNAQEBBQADKwAwKAIhAKcUvrdzek84BGT1tL5N0SCaknnmAmuU4WPG3tdBsz8LAgMBAAE=';

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode(array(
                'id'            => getenv('integration_id'),
                'login'         => 'login',
                'name'          => $name,
                'channel_sms'   => false,
                'channel_call'  => false,
                'channel_email' => false,
                'channel_totp'  => true,
                'public_key'    => $publicKey,
                'private_key'   => $privateKey,
            ));

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::CREATED));
        }

        $integration = $userZone->createIntegration($name);

        $this->assertInstanceOf('\TwoFAS\UserZone\Integration', $integration);
        if ($this->isDevelopmentEnvironment()) {
            $this->assertEquals(getenv('integration_id'), $integration->getId());
            $this->assertEquals('login', $integration->getLogin());
        }
        $this->assertNotEmpty($integration->getId());
        $this->assertNotEmpty($integration->getLogin());
        $this->assertEquals($name, $integration->getName());
        $this->assertNotNull($integration->getPublicKey());
        $this->assertNotNull($privateKey, $integration->getPrivateKey());
        $this->assertFalse($integration->getChannel('sms'));
        $this->assertFalse($integration->getChannel('call'));
        $this->assertFalse($integration->getChannel('email'));
        $this->assertTrue($integration->getChannel('totp'));
    }

    public function testGetIntegration()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        $privateKey = 'MIGqAgEAAiEApxS+t3N6TzgEZPW0vk3RIJqSeeYCa5ThY8be10GzPwsCAwEAAQIgdvVROIJSkfabQlqiXmA';
        $publicKey  = 'MDwwDQYJKoZIhvcNAQEBBQADKwAwKAIhAKcUvrdzek84BGT1tL5N0SCaknnmAmuU4WPG3tdBsz8LAgMBAAE=';

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode(array(
                'id'            => getenv('integration_id'),
                'login'         => 'sdk-website',
                'name'          => 'name',
                'channel_sms'   => false,
                'channel_call'  => false,
                'channel_email' => false,
                'channel_totp'  => true,
                'public_key'    => $publicKey,
                'private_key'   => $privateKey
            ));

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::OK));
        }

        $integration = $userZone->getIntegration(getenv('integration_id'));
        $this->assertInstanceOf('\TwoFAS\UserZone\Integration', $integration);
        $this->assertEquals(getenv('integration_id'), $integration->getId());
        $this->assertEquals('sdk-website', $integration->getLogin());
        $this->assertEquals('name', $integration->getName());
        $this->assertFalse($integration->getChannel('sms'));
        $this->assertFalse($integration->getChannel('call'));
        $this->assertFalse($integration->getChannel('email'));
        $this->assertTrue($integration->getChannel('totp'));
        $this->assertEquals($publicKey, $integration->getPublicKey());
        $this->assertEquals($privateKey, $integration->getPrivateKey());
    }

    public function testUpdateIntegrationAndEnableChannel()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = array(
                'id'            => 1,
                'login'         => 'login',
                'name'          => 'new_name',
                'channel_sms'   => false,
                'channel_call'  => false,
                'channel_email' => true,
                'channel_totp'  => true
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::OK));
        }

        $integration = new Integration();
        $integration
            ->setId(getenv('integration_id'))
            ->setLogin('login')
            ->setName('name')
            ->setChannels(array(
                'sms'   => false,
                'call'  => false,
                'email' => false,
                'totp'  => true
            ));

        $integration
            ->setName('new_name')
            ->enableChannel('email');
        $userZone->updateIntegration($integration);

        $this->assertEquals(getenv('integration_id'), $integration->getId());
        $this->assertEquals('login', $integration->getLogin());
        $this->assertEquals('new_name', $integration->getName());
        $this->assertFalse($integration->getChannel('sms'));
        $this->assertFalse($integration->getChannel('call'));
        $this->assertTrue($integration->getChannel('email'));
        $this->assertTrue($integration->getChannel('totp'));
    }

    public function testUpdateIntegrationAndDisableChannel()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = array(
                'id'            => 1,
                'login'         => 'login',
                'name'          => 'new_name',
                'channel_sms'   => false,
                'channel_call'  => false,
                'channel_email' => false,
                'channel_totp'  => true
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::OK));
        }

        $integration = new Integration();
        $integration
            ->setId(getenv('integration_id'))
            ->setLogin('login')
            ->setName('name')
            ->setChannels(array(
                'sms'   => false,
                'call'  => false,
                'email' => true,
                'totp'  => true
            ));

        $integration
            ->setName('new_name')
            ->disableChannel('email');
        $userZone->updateIntegration($integration);

        $this->assertEquals(getenv('integration_id'), $integration->getId());
        $this->assertEquals('login', $integration->getLogin());
        $this->assertEquals('new_name', $integration->getName());
        $this->assertFalse($integration->getChannel('sms'));
        $this->assertFalse($integration->getChannel('call'));
        $this->assertFalse($integration->getChannel('email'));
        $this->assertTrue($integration->getChannel('totp'));
    }

    public function testCannotEnableIntegrationChannel()
    {
        $tokenType = TokenType::wordpress();
        $token     = new Token($tokenType->getType(), getenv('oauth_second_wordpress_token'), getenv('second_integration_id'));
        $storage   = new ArrayStorage();
        $storage->storeToken($token);

        $userZone   = $this->getEmptyUserZoneWithCustomStorage($storage);
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode(array(
                'error' => array(
                    'code' => 10001,
                    'msg'  => array(
                        'channel_sms' => array(
                            'validation.channel_enabling_rules'
                        )
                    )
                )
            ));

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::BAD_REQUEST));
        }

        $this->setExpectedException('\TwoFAS\UserZone\Exception\ValidationException');

        $integration = new Integration();
        $integration
            ->setId(getenv('second_integration_id'))
            ->setLogin('login')
            ->setName('name')
            ->setChannels(array(
                'sms'   => false,
                'call'  => false,
                'email' => false,
                'totp'  => true
            ));

        $integration->enableChannel('sms');
        $userZone->updateIntegration($integration);
    }

    public function testCannotDisableIntegrationChannel()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode(array(
                'error' => array(
                    'code' => 10001,
                    'msg'  => array(
                        'channel_sms' => array(
                            'validation.channel_disabling_rules'
                        )
                    )
                )
            ));

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::BAD_REQUEST));
        }

        $this->setExpectedException('\TwoFAS\UserZone\Exception\ValidationException');

        $integration = new Integration();
        $integration
            ->setId(getenv('integration_id'))
            ->setLogin('login')
            ->setName('name')
            ->setChannels(array(
                'sms'   => false,
                'call'  => false,
                'email' => false,
                'totp'  => true
            ));

        $integration->disableChannel('totp');
        $userZone->updateIntegration($integration);
    }

    public function testForceDisableIntegrationChannel()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = array(
                'id'            => getenv('integration_id'),
                'login'         => 'login',
                'name'          => 'name',
                'channel_sms'   => false,
                'channel_call'  => false,
                'channel_email' => true,
                'channel_totp'  => false
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::OK));
        }

        $integration = new Integration();
        $integration
            ->setId(getenv('integration_id'))
            ->setLogin('login')
            ->setName('name')
            ->setChannels(array(
                'sms'   => false,
                'call'  => false,
                'email' => true,
                'totp'  => true
            ));

        $integration->forceDisableChannel('totp');
        $userZone->updateIntegration($integration);

        $this->assertFalse($integration->getChannel('sms'));
        $this->assertFalse($integration->getChannel('call'));
        $this->assertTrue($integration->getChannel('email'));
        $this->assertFalse($integration->getChannel('totp'));
    }

    public function testCreateIntegrationThrowsValidationExceptionIfDataIsInvalid()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = $this->getExpectedValidationBody(
                array(
                    'name' => array('validation.string')
                )
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::BAD_REQUEST));
        }

        $this->setExpectedException('\TwoFAS\UserZone\Exception\ValidationException');

        $userZone->createIntegration('a');
    }
}