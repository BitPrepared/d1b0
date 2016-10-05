<?php

namespace Tests;

use Silex\WebTestCase;
use JsonSchema\Validator;

class PartTest extends WebTestCase
{
    use AbstractAppTest;

    /*verifico che una parte si possa scaricare correttamente*/
    public function testGetWorkspacePart(){
        $schema = __DIR__.'/../../../../api/schemas/part.json';

        $client = $this->createClient();
        $client = $this->logIn($client);

        $crawler = $client->request('GET', '/api/v1/workspace/6/part/1');
        $response = $client->getResponse();

        //print_r($response);
        $data = $client->getResponse()->getContent();
        $validator = $this->askValidation($data,$schema);

        $assert = $this->evalValidation($validator);
        $this->assertTrue($assert);
    }
}
