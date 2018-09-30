<?php

namespace App\Tests;

use App\Model\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class SafeboxControllerTest extends ApiTestCase
{

    /**
     * Test create safebox
     */
    public function testSafeboxCreateResponse()
    {
        $data = [
            'name' => 'Adsmurai Safebox 01',
            'password' => 'adsmuraiExamplePassword'
        ];

        $response = $this->request('safebox', 'POST', json_encode($data));

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $content = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('id', $content);
        $this->assertEquals(count($content), 1);

    }
}