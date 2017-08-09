<?php

use TwoFAS\UserZone\HttpCodes;
use TwoFAS\UserZone\Response\ResponseGenerator;

class PasswordResetTest extends UserZoneBase
{
    public function testResetPasswordReturnsNoContentObjectIfEmailExists()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = '';
            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::NO_CONTENT));
        }

        $noContent = $userZone->resetPassword(getenv('client_email'));

        $this->assertInstanceOf('\TwoFAS\UserZone\NoContent', $noContent);
    }

    public function testResetPasswordThrowsValidatorErrorIfValidEmailIsNotProvided()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = $this->getExpectedValidationBody(
                array('email' => array('validation.required')
                )
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::BAD_REQUEST));
        }

        $this->setExpectedException('\TwoFAS\UserZone\Exception\ValidationException');

        $userZone->resetPassword('test');
    }

    public function testResetPasswordThrowsNotFoundExceptionIfEmailIsNotFound()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = array(
                'error' => array(
                    'code' => 10404,
                    'msg'  => 'No data matching given criteria'
                )
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::NOT_FOUND));
        }

        $this->setExpectedException('\TwoFAS\UserZone\Exception\NotFoundException');

        $userZone->resetPassword('notfound@2fas.com');
    }
}