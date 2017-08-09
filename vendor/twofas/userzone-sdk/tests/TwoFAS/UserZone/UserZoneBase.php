<?php

use TwoFAS\UserZone\Errors;
use TwoFAS\UserZone\HttpClient\CurlClient;
use TwoFAS\UserZone\OAuth\Interfaces\TokenStorage;
use TwoFAS\UserZone\OAuth\TokenType;
use TwoFAS\UserZone\UserZone;

abstract class UserZoneBase extends PHPUnit_Framework_TestCase
{
    private $env;
    private $baseUrl;

    protected function setUp()
    {
        parent::setUp();

        $this->env     = getenv('env');
        $this->baseUrl = getenv('base_url');
    }

    /**
     * @param array $headers
     *
     * @return UserZone
     */
    protected function getUserZone(array $headers = array())
    {
        $tokenStorage = new FilledStorage();
        $userZone     = new UserZone($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $userZone->setBaseUrl($this->baseUrl);

        return $userZone;
    }

    /**
     * @param array $headers
     *
     * @return UserZone
     */
    protected function getUserZoneWithRandomKeys(array $headers = array())
    {
        $tokenStorage = new RandomStorage();
        $userZone     = new UserZone($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $userZone->setBaseUrl($this->baseUrl);

        return $userZone;
    }

    /**
     * @param array $headers
     *
     * @return UserZone
     */
    protected function getUserZoneWithRevokedKeys(array $headers = array())
    {
        $tokenStorage = new RevokedStorage();
        $userZone     = new UserZone($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $userZone->setBaseUrl($this->baseUrl);

        return $userZone;
    }

    /**
     * @param array $headers
     *
     * @return UserZone
     */
    protected function getEmptyUserZone(array $headers = array())
    {
        list($userZone) = $this->getEmptyUserZoneAndStorage($headers);

        return $userZone;
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    protected function getEmptyUserZoneAndStorage(array $headers = array())
    {
        $tokenStorage = new ArrayStorage();
        $userZone     = new UserZone($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $userZone->setBaseUrl($this->baseUrl);

        return array($userZone, $tokenStorage);
    }

    /**
     * @param TokenStorage $tokenStorage
     * @param array        $headers
     *
     * @return UserZone
     */
    protected function getEmptyUserZoneWithCustomStorage(TokenStorage $tokenStorage, array $headers = array())
    {
        $userZone = new UserZone($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $userZone->setBaseUrl($this->baseUrl);

        return $userZone;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|CurlClient
     */
    protected function getHttpClient()
    {
        if ($this->isDevelopmentEnvironment()) {
            return $this->getMockBuilder('\TwoFAS\UserZone\HttpClient\CurlClient')->getMock();
        }

        return new CurlClient();
    }

    /**
     * @param array $rules
     *
     * @return array
     */
    protected function getExpectedValidationBody(array $rules)
    {
        return array(
            'error' => array(
                'code' => Errors::USER_INPUT_ERROR,
                'msg'  => $rules
            )
        );
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    private function addEnvHeaders(array $headers)
    {
        if ($this->isDevelopmentEnvironment()) {
            return $headers;
        }

        return array_merge(
            $headers, array('x-forwarded-proto' => 'https')
        );
    }

    /**
     * @return bool
     */
    protected function isDevelopmentEnvironment()
    {
        return 'dev' === $this->env;
    }
}
