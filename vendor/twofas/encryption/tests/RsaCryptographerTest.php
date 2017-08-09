<?php

namespace TwoFAS\Encryption;

class RsaCryptographerTest extends \PHPUnit_Framework_TestCase
{
    const KEY_PUBLIC  = 'LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0KTUR3d0RRWUpLb1pJaHZjTkFRRUJCUUFES3dBd0tBSWhBTVFrSjI3T2xubTVJQWZuSHQvbHFnZFZ5TUg3SnFPLwplMG8yTHVYQmtqZ0hBZ01CQUFFPQotLS0tLUVORCBQVUJMSUMgS0VZLS0tLS0K';
    const KEY_PRIVATE = 'LS0tLS1CRUdJTiBSU0EgUFJJVkFURSBLRVktLS0tLQpNSUdyQWdFQUFpRUF4Q1FuYnM2V2Via2dCK2NlMytXcUIxWEl3ZnNtbzc5N1NqWXU1Y0dTT0FjQ0F3RUFBUUloCkFKbnl3cHlmTzg1WWRvS2Ria2RRTE8zcXRxNFNrbTRZVVdjajBCamY4ZWs1QWhFQTYrSVE4OHFxektySVVOaEsKUFRjd0t3SVJBTlRlYTlOVnFKbU52SjdpMEllRERaVUNFQ2VVcm8yS0EzRUdjMGlGa3FlRS9ETUNFRDlJUEMvZwppRFhXRUJ2Lys5UTlYcDBDRVFEZWhQTWZ3UnVJVFRwY2E1VWo0c1oxCi0tLS0tRU5EIFJTQSBQUklWQVRFIEtFWS0tLS0tCg==';

    public function testEncryptDecrypt()
    {
        $cryptographer = new RsaCryptographer(self::KEY_PUBLIC, self::KEY_PRIVATE);

        $encrypted = $cryptographer->encryptToBase64('foobar');

        $this->assertEquals('foobar', $cryptographer->decryptBase64($encrypted));
    }

    public function testDecryptError()
    {
        $this->setExpectedException('\TwoFAS\Encryption\Exceptions\RsaDecryptException');

        $privateKey = 'LS0tLS1CRUdJTiBSU0EgUFJJVkFURSBLRVktLS0tLQpNSUdzQWdFQUFpRUFwanNtU000T0ZROE4vQ2kzRHB4RFpqMi84VUZyS1RTL3pLVVZYWDJ2OXZFQ0F3RUFBUUlnCkE0ZmVHRTNJSHNwakVhZ0x1MU8yV3J6Ukp6NDVGeVZ4SERVaFNOR081OEVDRVFEU21wNTF0a3FBTWxieko4NS8KWEVkdkFoRUF5Zy82b25NbG1JMG9DcXBCOXdWM253SVJBS2xvOHpxaExvQzgvYkNQUHM2NGZrVUNFUUN4OGg2RQpQNm1GVGhKTVNpSXJtNW43QWhFQXJZSTRsQjJXYzM1RFNtNlA1a1BqSWc9PQotLS0tLUVORCBSU0EgUFJJVkFURSBLRVktLS0tLQo=';

        $cryptographer = new RsaCryptographer(self::KEY_PUBLIC, $privateKey);
        $encrypted = $cryptographer->encryptToBase64('foobar');

        $cryptographer->decryptBase64($encrypted);
    }
}
