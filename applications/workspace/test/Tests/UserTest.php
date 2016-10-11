<?php

namespace Tests;

use Silex\WebTestCase;
use JsonSchema\Validator;

class UserTest extends WebTestCase
{
    use AbstractAppTest;

    public function testGetUser(){
        $schema = __DIR__.'/../../../../api/schemas/userInfo.json';

        $client = $this->createClient();
        $client = $this->logIn($client);

        $crawler = $client->request('GET', '/api/v1/user/1');
        $response = $client->getResponse();

        //print_r($response);
        $data = $client->getResponse()->getContent();
        $validator = $this->askValidation($data, $schema);

        $assert = $this->evalValidation($validator);
        $this->assertTrue($assert);
    }

    public function testPostBadge(){
        $client = $this->createClient();
        $client = $this->logIn($client);

        $badge_id=5;

        $badge = '{
              "id": '.$badge_id.'
          }';

        $client->request(
            'POST',
            '/api/v1/user/1/badge',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $badge);

        $response = $client->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
        return $badge_id;
    }
    public function testGetBadge(){
        $schema = __DIR__.'/../../../../api/schemas/badgeUser.json';

        $badge_id = $this->testPostBadge();
        $client = $this->createClient();
        $client = $this->logIn($client);

        $crawler = $client->request('GET', '/api/v1/user/1/badge/'.$badge_id);
        $response = $client->getResponse();

        //print_r($response);
        $data = $client->getResponse()->getContent();

        //print_r($data);
        $validator = $this->askValidation($data, $schema);

        $assert = $this->evalValidation($validator);
        $this->assertTrue($assert);
    }

    public function testDeleteBadge(){
        $client = $this->createClient();
        $client = $this->logIn($client);

        $badge_id = $this->testPostBadge();

        $client->request(
            'DELETE',
            '/api/v1/user/1/badge/'.$badge_id.'',
            [],
            [],
            [],
            '');

        $response = $client->getResponse();
        $this->assertEquals(204,$response->getStatusCode());
        return $badge_id;
    }

    public function testPostBadgeCompleted(){
        $client = $this->createClient();
        $client = $this->logIn($client);

        $badge_id = $this->testPostBadge();

        $client->request(
            'PATCH',
            '/api/v1/user/1/badge/'.$badge_id.'/completed',
            [],
            [],
            [],
            '');

        $response = $client->getResponse();
        $this->assertEquals(204,$response->getStatusCode());
        return $badge_id;
    }

    public function testGetUserTicket(){
        $schema = __DIR__.'/../../../../api/schemas/ticketList.json';

        $client = $this->createClient();
        $client = $this->logIn($client);

        $crawler = $client->request('GET', '/api/v1/user/1/ticket');
        $response = $client->getResponse();

        //print_r($response);
        $data = $client->getResponse()->getContent();
        $validator = $this->askValidation($data, $schema);

        $assert = $this->evalValidation($validator);
        $this->assertTrue($assert);
    }
}
