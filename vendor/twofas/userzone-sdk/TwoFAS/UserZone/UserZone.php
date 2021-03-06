<?php

namespace TwoFAS\UserZone;

use InvalidArgumentException;
use TwoFAS\UserZone\Exception\Exception;
use TwoFAS\UserZone\Exception\NotFoundException;
use TwoFAS\UserZone\Exception\PasswordResetAttemptsRemainingIsReachedException;
use TwoFAS\UserZone\HttpClient\ClientInterface;
use TwoFAS\UserZone\HttpClient\CurlClient;
use TwoFAS\UserZone\OAuth\Interfaces\TokenStorage;
use TwoFAS\UserZone\OAuth\Token;
use TwoFAS\UserZone\OAuth\TokenNotFoundException;
use TwoFAS\UserZone\OAuth\TokenType;
use TwoFAS\UserZone\Response\Response;

class UserZone
{
    /**
     * @var string
     */
    const VERSION = '2.0.5';

    /**
     * @var string
     */
    const API_VERSION = 'v2';

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var string
     */
    private $baseUrl = 'https://twofas-server.herokuapp.com';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var Hydrator
     */
    private $hydrator;

    /**
     * @var TokenType
     */
    private $specificIntegrationTokenType;

    /**
     * @var array
     */
    private $headers = array(
        'Content-Type' => 'application/json',
        'Sdk-Version'  => self::VERSION
    );

    /**
     * @param TokenStorage $tokenStorage
     * @param TokenType    $specificIntegration
     * @param array        $headers
     */
    public function __construct(TokenStorage $tokenStorage, TokenType $specificIntegration, array $headers = array())
    {
        $this->tokenStorage                 = $tokenStorage;
        $this->specificIntegrationTokenType = $specificIntegration;
        $this->httpClient                   = new CurlClient();
        $this->hydrator                     = new Hydrator();

        $this->addHeaders($headers);
    }

    /**
     * @param  string $url
     *
     * @return UserZone
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * @param ClientInterface $httpClient
     *
     * @return UserZone
     */
    public function setHttpClient(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * @return Client
     *
     * @throws Exception
     */
    public function getClient()
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'GET',
            $this->createEndpoint('/me')
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            return $this->hydrator->getClientFromResponseData($response->getData());
        }

        throw $response->getError();
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $passwordConfirmation
     * @param string $source
     *
     * @return Client
     *
     * @throws Exception
     */
    public function createClient($email, $password, $passwordConfirmation, $source)
    {
        $response = $this->call(
            null,
            'POST',
            $this->createEndpoint('/me'), array(
                'email'                 => $email,
                'password'              => $password,
                'password_confirmation' => $passwordConfirmation,
                'source'                => $source
            )
        );

        if ($response->matchesHttpCode(HttpCodes::CREATED)) {
            return $this->hydrator->getClientFromResponseData($response->getData());
        }

        throw $response->getError();
    }

    /**
     * @param int $integrationId
     *
     * @return Integration
     *
     * @throws Exception
     */
    public function getIntegration($integrationId)
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'GET',
            $this->createEndpoint('/integrations/' . $integrationId)
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            return $this->hydrator->getIntegrationFromResponseData($response->getData());
        }

        throw $response->getError();
    }

    /**
     * @param string $name
     *
     * @return Integration
     *
     * @throws Exception
     */
    public function createIntegration($name)
    {
        $response = $this->call(
            TokenType::setup(),
            'POST',
            $this->createEndpoint('/integrations'), array(
                'name' => $name
            )
        );

        if ($response->matchesHttpCode(HttpCodes::CREATED)) {
            return $this->hydrator->getIntegrationFromResponseData($response->getData());
        }

        throw $response->getError();
    }


    /**
     * @param Integration $integration
     *
     * @return Integration
     *
     * @throws Exception
     */
    public function updateIntegration(Integration $integration)
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'PUT',
            $this->createEndpoint('/integrations/' . $integration->getId()), $integration->toArray()
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            return $integration;
        }

        throw $response->getError();
    }

    /**
     * @param int    $integrationId
     * @param string $name
     *
     * @return Key
     *
     * @throws Exception
     */
    public function createKey($integrationId, $name)
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'POST',
            $this->createEndpoint("/integrations/{$integrationId}/keys"), array(
                'name' => $name,
                'type' => 'production'
            )
        );

        if ($response->matchesHttpCode(HttpCodes::CREATED)) {
            $responseData = $response->getData();
            return new Key($responseData['token']);
        }

        throw $response->getError();
    }

    /**
     * @param Client $client
     *
     * @return Card
     *
     * @throws Exception
     * @throws NotFoundException
     */
    public function getPrimaryCard(Client $client)
    {
        if (is_null($client->getPrimaryCardId())) {
            throw new NotFoundException('No data matching given criteria');
        }

        $response = $this->call(
            $this->specificIntegrationTokenType,
            'GET',
            $this->createEndpoint("/billing/cards/{$client->getPrimaryCardId()}")
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            return $this->hydrator->getCardFromResponseData($response->getData());
        }

        throw $response->getError();
    }

    /**
     * @param string $email
     *
     * @return NoContent
     *
     * @throws PasswordResetAttemptsRemainingIsReachedException
     * @throws Exception
     */
    public function resetPassword($email)
    {
        $response = $this->call(
            null,
            'POST',
            $this->createEndpoint('/me/password-reset'), array('email' => $email)
        );

        if ($response->matchesHttpCode(HttpCodes::NO_CONTENT)) {
            return new NoContent();
        }

        throw $response->getError();
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @throws Exception
     */
    public function generateOAuthSetupToken($email, $password)
    {
        $response = $this->httpClient->request(
            'POST',
            $this->createEndpoint('/me/login'),
            array(
                'email'    => $email,
                'password' => $password,
                'scope'    => TokenType::SETUP
            ),
            $this->headers
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            $responseData = $response->getData();
            $accessToken  = $responseData['token']['accessToken'];
            $scopes       = $responseData['token']['token']['scopes'];

            if (array(TokenType::SETUP) === $scopes) {
                $token = new Token(TokenType::SETUP, $accessToken, 0);
                $this->tokenStorage->storeToken($token);

                return;
            }
        }

        throw $response->getError();
    }

    /**
     * @param string $email
     * @param string $password
     * @param int    $integrationId
     *
     * @throws Exception
     */
    public function generateIntegrationSpecificToken($email, $password, $integrationId)
    {
        $response = $this->httpClient->request(
            'POST',
            $this->createEndpoint('/me/login/integration'),
            array(
                'email'          => $email,
                'password'       => $password,
                'scope'          => $this->specificIntegrationTokenType->getType(),
                'integration_id' => $integrationId
            ),
            $this->headers
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            $responseData = $response->getData();
            $accessToken  = $responseData['token']['accessToken'];
            $scopes       = $responseData['token']['token']['scopes'];

            if (array($this->specificIntegrationTokenType->getType()) === $scopes) {
                $token = new Token($this->specificIntegrationTokenType->getType(), $accessToken, $integrationId);
                $this->tokenStorage->storeToken($token);

                return;
            }
        }

        throw $response->getError();
    }

    /**
     * @param array $headers
     *
     * @throws InvalidArgumentException
     */
    private function addHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            $key = $this->normalizeHeader($header);

            if (array_key_exists($key, $this->headers)) {
                throw new InvalidArgumentException('Existing header could not be changed: ' . $key);
            }

            $this->headers[$key] = $value;
        }
    }

    /**
     * @param string $header
     *
     * @return string
     */
    private function normalizeHeader($header)
    {
        $parts = explode('-', trim($header));

        $parts = array_map(function($part) {
            return ucfirst(strtolower($part));
        }, $parts);

        return implode('-', $parts);
    }

    /**
     * @param string $suffix
     *
     * @return string
     */
    private function createEndpoint($suffix)
    {
        return $this->baseUrl . '/' . self::API_VERSION . $suffix;
    }

    /**
     * @param TokenType|null $tokenType
     * @param string         $method
     * @param string         $endpoint
     * @param array          $data
     *
     * @return Response
     *
     * @throws Exception
     * @throws TokenNotFoundException
     */
    private function call($tokenType, $method, $endpoint, array $data = array())
    {
        if (null === $tokenType) {
            $this->clearAuthorizationToken();
        } else {
            $this->setAuthorizationToken($this->tokenStorage->retrieveToken($tokenType->getType()));
        }

        return $this->httpClient->request($method, $endpoint, $data, $this->headers);
    }

    /**
     * @param Token $token
     */
    private function setAuthorizationToken($token)
    {
        $this->headers['Authorization'] = 'Bearer ' . $token->getAccessToken();
    }

    private function clearAuthorizationToken()
    {
        unset($this->headers['Authorization']);
    }
}