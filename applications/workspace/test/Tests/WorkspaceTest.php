<?php

namespace Tests;

use Silex\WebTestCase;
use JsonSchema\Validator;

class WorkspaceTest extends WebTestCase
{
    use AbstractAppTest;

    public function testGetWorkSpace(){
        $schema = __DIR__.'/../../../../api/schemas/workspace.json';

        $client = $this->createClient();
        $client = $this->logIn($client);

        $crawler = $client->request('GET', '/api/v1/workspace/1');
        $response = $client->getResponse();
        $data = $client->getResponse()->getContent();
        $validator = $this->askValidation($data,$schema);

        $assert = $this->evalValidation($validator);
        $this->assertTrue($assert);
    }
    public function testGetWorkspaceList(){
        $schema = __DIR__.'/../../../../api/schemas/workspaceList.json';

        $client = $this->createClient();
        $client = $this->logIn($client);

        $crawler = $client->request('GET', '/api/v1/workspace/');
        $response = $client->getResponse();

        $data = $client->getResponse()->getContent();
        $validator = $this->askValidation($data,$schema);

        $assert = $this->evalValidation($validator);
        $this->assertTrue($assert);
    }

    public function testGetWorkspaceShare(){
        $schema = __DIR__.'/../../../../api/schemas/workspaceShare.json';

        $client = $this->createClient();
        $client = $this->logIn($client);

        $crawler = $client->request('GET', '/api/v1/workspace/1/share');
        $response = $client->getResponse();

        //print_r($response);
        $data = $client->getResponse()->getContent();
        $validator = $this->askValidation($data,$schema);

        $assert = $this->evalValidation($validator);
        $this->assertTrue($assert);
    }
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

    public function testPostWorkspace(){
        //$schema = __DIR__.'/../../../../api/schemas/workspace.json';

        $client = $this->createClient();
        $client = $this->logIn($client);

        $wp ='{
              "title":"Sopraelevata",
              "description":"I Sarchiaponi sono pronti a lanciarsi nella costruzione di una sopraelevata",
              "environment": 22,
              "team": {
                  "patrol":"Sarchiaponi",
                  "group":"Bologna 18",
                  "unit": "Marco Polo"
              },
              "badges": [ 5, 38, 99]
          }';

        $client->request(
          'POST',
          '/api/v1/workspace/',
          [],
          [],
          ['CONTENT_TYPE' => 'application/json'],
          $wp);

        $response = $client->getResponse();

        $data = $client->getResponse()->getContent();
        $id = json_decode($data);

        $id=$id->id;

        $this->assertTrue(is_numeric($id));

        return $id;
    }
    public function testPutWorkspace(){
        //$schema = __DIR__.'/../../../../api/schemas/workspace.json';

        $id = $this->testPostWorkspace();
        $wp ='{
              "id":'.$id.',
              "title":"Sopraelevata_EDIT",
              "description":"I Sarchiaponi sono pronti a lanciarsi nella costruzione di una sopraelevata",
              "environment": 25,
              "team": {
                  "patrol":"Sarchiaponini",
                  "group":"Bologna 17",
                  "unit": "Marco Polo2"
              },
              "badges": [ 5, 38, 99]
          }';

        $client = $this->createClient();
        $client = $this->logIn($client);

        print_r("\nID: ".$id."\n");
        $client->request(
          'PUT',
          '/api/v1/workspace/'.$id.'',
          [],
          [],
          ['CONTENT_TYPE' => 'application/json'],
          $wp);

        $response = $client->getResponse();
        print($response);
        //$data = $client->getResponse()->getContent();
        //$id = json_decode($data);

        //$id=$id->id;

        $this->assertTrue($response->isOk());
    }
    public function testDeleteWorkspace(){
        //$schema = __DIR__.'/../../../../api/schemas/workspace.json';
        $id = $this->testPostWorkspace();
        $client = $this->createClient();
        $client = $this->logIn($client);

        $client->request(
          'DELETE',
          '/api/v1/workspace/'.$id.'',
          [],
          [],
          [],
          '');

        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
    }
}
