<?php

namespace App\Tests;

use App\Entity\Safebox;
use App\Model\ApiTestCase;
use App\Model\EncryptUtils;
use Symfony\Component\HttpFoundation\Response;

class SafeboxControllerTest extends ApiTestCase
{

    /**
     * Test create safebox
     */
    public function testSafeboxCreateResponse()
    {
        $repository = self::$entityManager->getRepository(Safebox::class);

        $initialCount = count($repository->findAll());

        $data = [
            'name' => 'Safebox 01',
            'password' => 'safeboxExamplePassword'
        ];

        $response = $this->request('safebox', 'POST', json_encode($data));

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $content = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('id', $content);
        $this->assertEquals(count($content), 1);

        $this->assertEquals($initialCount + 1, count($repository->findAll()));

        // Test create an existing safebox
        $response = $this->request('safebox', 'POST', json_encode($data));
        $this->assertEquals(409, $response->getStatusCode());

        // Test bad formatted data
        $data['password'] = '123456';
        $response = $this->request('safebox', 'POST', json_encode($data));
        $this->assertEquals(422, $response->getStatusCode());

    }

    /**
     * Test open safebox
     */
    public function testSafeboxOpenCreate()
    {
        $repository = self::$entityManager->getRepository(Safebox::class);

        $response = $this->request(
            'safebox/1/open',
                'GET',
                '',
                ['Authorization' => "Bearer safeboxExamplePassword"]
        );
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $content = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('token', $content);
        $this->assertEquals(count($content), 1);

        /** @var Safebox $safebox */
        $safebox = $repository->find(1);

        $date = new \DateTime();
        $date->add(new \DateInterval('PT180S'));

        $this->assertEquals($date->format('i'), $safebox->getToken()->getExpirationTime()->format('i'));

        // Test encrypted token expiration date
        /** @var EncryptUtils $encryptUtils */
        $client = static::createClient();
        $encryptUtils = $client->getContainer()->get('app.encrypt');
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($encryptUtils->decrypt($safebox->getToken()->getExpirationTimeEncrypted()));
        $this->assertEquals(
            $dateTime,
            $safebox->getToken()->getExpirationTime()
        );

        // Test expiration time parameter
        $data = [
            'name' => 'Safebox 02',
            'password' => 'safeboxExamplePassword'
        ];
        $this->request('safebox', 'POST', json_encode($data));
        $this->request(
            'safebox/2/open?expirationTime=120',
            'GET',
            '',
            ['Authorization' => "Bearer safeboxExamplePassword"]
        );

        $safebox = $repository->find(2);

        $date = new \DateTime();
        $date->add(new \DateInterval('PT120S'));

        $this->assertEquals($date->format('i'), $safebox->getToken()->getExpirationTime()->format('i'));

        // Test safebox does not exists
        $response = $this->request('safebox/10/open');
        $this->assertEquals(404, $response->getStatusCode());

        // Test authentication
        $response = $this->request('safebox/2/open?expirationTime=120');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());

    }

    public function testPostSafeboxItem()
    {
        $repository = self::$entityManager->getRepository(Safebox::class);

        $data = [
            'item' => 'New safebox content'
        ];

        // Test invalid token
        $response = $this->request('safebox/1', 'POST', json_encode($data));
        $this->assertEquals(401, $response->getStatusCode());

        // Test safebox does not exist
        $safebox = $repository->find(1);
        $response = $this->request(
            'safebox/10',
                'POST',
                json_encode($data),
            ['Authorization' => "Bearer {$safebox->getToken()->getToken()}"]
        );
        $this->assertEquals(404, $response->getStatusCode());

        // Test malformed data
        $response = $this->request(
            'safebox/1',
            'POST',
            json_encode([]),
            ['Authorization' => "Bearer {$safebox->getToken()->getToken()}"]
        );
        $this->assertEquals(400, $response->getStatusCode());

        // Test database insert
        $response = $this->request(
            'safebox/1',
            'POST',
            json_encode($data),
            ['Authorization' => "Bearer {$safebox->getToken()->getToken()}"]
        );
        $this->assertEquals(200, $response->getStatusCode());
        /** @var Safebox $safebox */
        $safebox = $repository->find(1);
        $this->assertEquals('New safebox content', $safebox->getItems()[0]->getContent());

        // Test encrypted item content
        /** @var EncryptUtils $encryptUtils */
        $client = static::createClient();
        $encryptUtils = $client->getContainer()->get('app.encrypt');
        $this->assertEquals(
            'New safebox content',
            $encryptUtils->decrypt($safebox->getItems()[0]->getContentEncrypted())
        );

        // Test locked safebox
        $response = $this->request(
            'safebox/2',
            'POST',
            json_encode([]),
            ['Authorization' => "Bearer invalid.token"]
        );
        $this->assertEquals(401, $response->getStatusCode());
        $response = $this->request(
            'safebox/2',
            'POST',
            json_encode([]),
            ['Authorization' => "Bearer invalid.token"]
        );
        $this->assertEquals(401, $response->getStatusCode());
        $response = $this->request(
            'safebox/2',
            'POST',
            json_encode([]),
            ['Authorization' => "Bearer invalid.token"]
        );
        $this->assertEquals(401, $response->getStatusCode());
        $response = $this->request(
            'safebox/2',
            'POST',
            json_encode([]),
            ['Authorization' => "Bearer invalid.token"]
        );
        $this->assertEquals(423, $response->getStatusCode());
    }

    public function testGetSafeboxContent()
    {
        $repository = self::$entityManager->getRepository(Safebox::class);
        $safebox = $repository->find(1);

        // Test invalid token
        $response = $this->request('safebox/1');
        $this->assertEquals(401, $response->getStatusCode());

        // Test safebox does not exist
        $response = $this->request(
            'safebox/10',
            'GET',
            json_encode([]),
            ['Authorization' => "Bearer {$safebox->getToken()->getToken()}"]
        );
        $this->assertEquals(404, $response->getStatusCode());

        // Test response
        $response = $this->request(
            'safebox/1',
            'GET',
            json_encode([]),
            ['Authorization' => "Bearer {$safebox->getToken()->getToken()}"]
        );
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $content = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('items', $content);
        $this->assertEquals(count($content), 1);
        $this->assertEquals('New safebox content', $content['items'][0]);

        // Test locked safebox
        $response = $this->request('safebox/1');
        $this->assertEquals(401, $response->getStatusCode());
        $response = $this->request('safebox/1');
        $this->assertEquals(423, $response->getStatusCode());

    }
}