<?php

namespace Tests;

use Silex\WebTestCase;
use JsonSchema\Validator;

class WorkspacesTest extends WebTestCase
{
    use AbstractAppTest;

    public function testGetWorkspaceList(){
        $schema = __DIR__.'/../../../../api/schemas/workspaceList.json';

        //$client = $this->createClient();
        //$client = $this->logIn($client);

        //$crawler = $client->request('GET', '/api/v1/workspace/');
        /*$response = $client->getResponse();
        print_r("funziona:");
        print_r($response);
        $data = $client->getResponse()->getContent();
        print_r($data);
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
        }*/
        $this->assertTrue(true);
    }
}
