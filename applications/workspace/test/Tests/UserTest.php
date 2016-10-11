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
}
