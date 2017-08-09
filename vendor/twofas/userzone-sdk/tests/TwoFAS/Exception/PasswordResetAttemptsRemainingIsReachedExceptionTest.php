<?php

use TwoFAS\UserZone\Exception\PasswordResetAttemptsRemainingIsReachedException;
use TwoFAS\UserZone\Response\ResponseGenerator;
use TwoFAS\UserZone\HttpCodes;
use TwoFAS\UserZone\HttpClient\CurlClient;

class PasswordResetAttemptsRemainingIsReachedExceptionTest extends UserZoneBase
{
    public function testException()
    {
        $userZone   = $this->getUserZone();
        $httpClient = $this->getHttpClient();
        $userZone->setHttpClient($httpClient);

        $minutesToNextReset = 12;

        $response = json_encode(array('error' => array(
            'code'    => 14403,
            'msg'     => 'Limit of password reset attempts is already reached',
            'payload' => array(
                'minutes_to_next_reset' => $minutesToNextReset
            )
        )));

        $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::FORBIDDEN));

        try {
            $userZone->resetPassword('foo@bar.com');

            $this->fail('PasswordResetAttemptsRemainingIsReachedException not be thrown');

        } catch (PasswordResetAttemptsRemainingIsReachedException $exception) {
            $this->assertEquals(14403, $exception->getCode());
            $this->assertEquals('Limit of password reset attempts is already reached', $exception->getMessage());
            $this->assertEquals($minutesToNextReset, $exception->getMinutesToNextReset());
        }
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|CurlClient
     */
    protected function getHttpClient()
    {
        return $this->getMockBuilder('\TwoFAS\UserZone\HttpClient\CurlClient')->getMock();
    }
}
