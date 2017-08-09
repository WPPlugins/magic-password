<?php

use TwoFAS\UserZone\OAuth\TokenType;
use TwoFAS\UserZone\UserZone;

class HeadersTest extends UserZoneBase
{
    public function testHeaders()
    {
        $headers = array(
            'APP-VERSION'    => '4.6.1',
            'app-Name'       => '2FAS WP',
            'app-URL'        => 'http://wordpress.local',
            'plugin-version' => '1.0.0'
        );

        $expectedHeaders = array(
            'Content-Type'   => 'application/json',
            'Sdk-Version'    => UserZone::VERSION,
            'App-Version'    => '4.6.1',
            'App-Name'       => '2FAS WP',
            'App-Url'        => 'http://wordpress.local',
            'Plugin-Version' => '1.0.0'
        );

        $tokenStorage = new ArrayStorage();
        $userZone     = new UserZone($tokenStorage, TokenType::wordpress(), $headers);

        $reflection          = new ReflectionClass($userZone);
        $reflection_property = $reflection->getProperty('headers');
        $reflection_property->setAccessible(true);
        $this->assertEquals($expectedHeaders, $reflection_property->getValue($userZone));
    }

    public function testOverrideHeaders()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Existing header could not be changed');

        $headers = array(
            'Content-Type'   => 'text/html',
            ' Content-Type'  => 'application/pdf',
            'Sdk-Version'    => '5.6.7',
            'Sdk-Version '   => '2.3.4',
            'App-Version'    => '4.6.1',
            'App-Name'       => '2FAS WP',
            'App-Url'        => 'http://wordpress.local',
            'Plugin-Version' => '1.0.0'
        );

        $tokenStorage = new ArrayStorage();
        new UserZone($tokenStorage, TokenType::wordpress(), $headers);
    }
}