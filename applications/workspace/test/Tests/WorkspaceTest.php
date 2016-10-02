<?php

namespace Tests;

use Silex\WebTestCase;

class WorkspaceTest extends WebTestCase
{
    use AbstractAppTest;

    public function testGetWorkSpace(){
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1/workspace/1');
        $this->assertTrue($client->getResponse()->isOk());
    }
}
