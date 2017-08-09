<?php

namespace TwoFAS\Encryption;

class AESIVGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInstantiable()
    {
        $ivGen = new AESIVGenerator();

        $this->assertInstanceOf('\TwoFAS\Encryption\AESIVGenerator', $ivGen);
    }


    public function testReturnsValidIVLength()
    {
        $ivGen = new AESIVGenerator();

        $this->assertEquals(strlen($ivGen->getIV()), 16);
    }

    public function testReturnsDistinctIVs()
    {
        $ivGen = new AESIVGenerator();
        $iv1 = $ivGen->getIV();
        $iv2 = $ivGen->getIV();

        $this->assertNotEquals($iv1, $iv2);
    }
}


