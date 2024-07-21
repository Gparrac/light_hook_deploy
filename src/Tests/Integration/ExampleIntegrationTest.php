<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class ExampleIntegrationTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
            $this->client = new Client(['base_uri' => ""]);
       
    }

    public function testExampleMessage()
    {
        $response = $this->client->request('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());

        $body = (string) $response->getBody();
        $this->assertEquals('Example Response', $body);
    }
}
