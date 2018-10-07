<?php

namespace App\Tests;

use App\Model\EncryptUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EncryptUtilsTest extends WebTestCase
{
    public function testEncryption()
    {
        $client = static::createClient();

        /** @var EncryptUtils $encryptUtils */
        $encryptUtils = $client->getContainer()->get('app.encrypt');

        self::assertEquals('test', $encryptUtils->decrypt($encryptUtils->encrypt('test')));
    }
}