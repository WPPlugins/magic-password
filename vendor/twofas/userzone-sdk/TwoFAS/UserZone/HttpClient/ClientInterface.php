<?php

namespace TwoFAS\UserZone\HttpClient;

use TwoFAS\UserZone\Response\Response;

interface ClientInterface
{
    /**
     * @param string $method
     * @param string $endpoint
     * @param array  $data
     * @param array  $headers
     *
     * @return Response
     */
    public function request($method, $endpoint, array $data = array(), array $headers = array());
}