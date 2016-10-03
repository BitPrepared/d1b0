<?php

namespace Tests;

use Silex\WebTestCase;
use JsonSchema\Validator;

class WorkspaceTest extends WebTestCase
{
    use AbstractAppTest;

    public function logIn($client){
        $client->request('POST',
                    '/api/v1/security/login',
                     [],
                    [],
                    ['CONTENT_TYPE' => 'application/json'],
                    '{"authMode":"Email","email":"ugo.ugo@ugo.it","name":"ugo","surname":"ugo","password":"cane"}');
        return $client;
    }

    public function testGetWorkSpace(){

        $schema = __DIR__.'/../../../../api/schemas/workspace.json';


        $client = $this->createClient();

        $client = $this->logIn($client);

        $crawler = $client->request('GET', '/api/v1/workspace/1');

        $response = $client->getResponse();
        $data = $client->getResponse()->getContent();

        $validator = $this->askValidation($data,$schema);

        if ($validator->isValid()) {

            echo "The supplied JSON validates against the schema.\n";
            $this->assertTrue(true);
        } else {
            echo "JSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                echo "[{$error['property']}] {$error['message']}\n";
            }
            $this->assertTrue(false);
        }
    }
}
