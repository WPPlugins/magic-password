<?php

namespace TwoFAS\Encryption;

class AESGeneratedKeyTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInstantiable()
    {
        $key = new AESGeneratedKey();

        $this->assertInstanceOf('\TwoFAS\Encryption\AESGeneratedKey', $key);
    }

    public function testReturnsValidKeyLength()
    {
        $key = new AESGeneratedKey();

        $this->assertEquals(strlen($key->getValue()), 128);
    }
}
