<?php

namespace Ecentria\Libraries\CoreRestBundle\Tests\Controller;

use Bazinga\Bundle\RestExtraBundle\Test\WebTestCase;

class PingControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        // head request
        $client->request('HEAD', '/ping');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());

        // content verification
        $client->request('GET', '/ping');
        $response = $client->getResponse();
        $this->assertJsonResponse($response);
        $this->assertEquals('["pong"]', $response->getContent());
    }
}
