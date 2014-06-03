<?php

namespace Ecentria\Libraries\CoreRestBundle\Tests\Controller;

use Bazinga\Bundle\RestExtraBundle\Test\WebTestCase;

class StatusControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        // head request
        $client->request('HEAD', '/status');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());

        // content verification
        $client->request('GET', '/status');
        $response = $client->getResponse();
        $this->assertJsonResponse($response);
        $this->assertEquals('["OK","0","All related services are available. All systems normal."]', $response->getContent());
    }
}
