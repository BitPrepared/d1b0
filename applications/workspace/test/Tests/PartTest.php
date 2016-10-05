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


    public function testPostWorkspacePart(){
        //$schema = __DIR__.'/../../../../api/schemas/part.json';

        $client = $this->createClient();
        $client = $this->logIn($client);
        $id = 50;
        $part ='{"part":[
                {
                    "type": "image",
                    "ref": "afaifnanabnawnfawba",
                    "hash":"'.hash($id."prova0").'"
                },
                {
                    "type": "image",
                    "ref": "awfaowapaegbgepng",
                    "hash":"'.hash($id."prova1").'"
                },
                {
                    "type": "text",
                    "ref": "afaafaafafafifnanabnawnfawba",
                    "hash":"'.hash($id."prova2").'"
                },
                {
                    "type": "video",
                    "ref": "aaafawafaggagaagegaa",
                    "hash":"'.hash($id."prova3").'"
                }
            ],
            "badge":[13,28]}';

        $client->request(
          'POST',
          '/api/v1/workspace/'.$id.'part',
          [],
          [],
          ['CONTENT_TYPE' => 'application/json'],
          $part);

        $response = $client->getResponse();

        //print_r($response);
        $data = $client->getResponse()->getContent();
        $validator = $this->askValidation($data,$schema);

        $assert = $this->evalValidation($validator);
        $this->assertTrue($assert);
    }
}
