<?php

namespace Tests;

use Silex\WebTestCase;
use JsonSchema\Validator;

class PartTest extends WorkspaceTest
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

        $id = $this->testPostWorkspace();
        $part ='{"part":[
                {
                    "type": "image",
                    "ref": "afaifnanabnawnfawba",
                    "hash":"'.hash("md5",$id."prova0").'"
                },
                {
                    "type": "image",
                    "ref": "awfaowapaegbgepng",
                    "hash":"'.hash("md5",$id."prova1").'"
                },
                {
                    "type": "text",
                    "ref": "afaafaafafafifnanabnawnfawba",
                    "hash":"'.hash("md5",$id."prova2").'"
                },
                {
                    "type": "video",
                    "ref": "aaafawafaggagaagegaa",
                    "hash":"'.hash("md5",$id."prova3").'"
                }
            ],
            "badges":[13,28]}';

        $client->request(
          'POST',
          '/api/v1/workspace/'.$id.'/part',
          [],
          [],
          ['CONTENT_TYPE' => 'application/json'],
          $part);

        $response = $client->getResponse();

        $data = $client->getResponse()->getContent();
        $id = json_decode($data);

        $id=$id->id;

        $this->assertTrue(is_numeric($id));
        return $id;
    }

    public function testPutWorkspacePart(){
        $client = $this->createClient();
        $client = $this->logIn($client);

        $id = $this->testPostWorkspace();
        $part_id = $this->testPostWorkspacePart();
        $part ='{
                "id":'.$part_id.',
                "part":[
                {
                    "type": "image",
                    "ref": "afaifnanabnawnfawba",
                    "hash":"'.hash("md5",$id."prova0").'"
                },
                {
                    "type": "image",
                    "ref": "awfaowapaegbgepng",
                    "hash":"'.hash("md5",$id."prova1").'"
                },
                {
                    "type": "text",
                    "ref": "blablablablabla",
                    "hash":"'.hash("md5",$id."prova2").'"
                }
            ],
            "badges":[13,30]}';

        $client->request(
          'PUT',
          '/api/v1/workspace/'.$id.'/part/'.$part_id.'',
          [],
          [],
          ['CONTENT_TYPE' => 'application/json'],
          $part);

        $response = $client->getResponse();
        $data = $client->getResponse()->getContent();
        $id = json_decode($data);

        $res_id=$id->id;

        $this->assertEquals($part_id,$res_id);
    }

    public function testDeleteWorkspacePart(){
        $client = $this->createClient();
        $client = $this->logIn($client);

        $id = $this->testPostWorkspace();
        $part_id = $this->testPostWorkspacePart();

        $client->request(
          'DELETE',
          '/api/v1/workspace/'.$id.'/part/'.$part_id.'',
          [],
          [],
          [],
          '');

        $response = $client->getResponse();
        $this->assertTrue($response->isOk());

        $crawler = $client->request('GET', '/api/v1/workspace/'.$id);

        print_r($client->getResponse());
        $data = $client->getResponse()->getContent();

        $js = json_decode($data);
        $trovato = false;

        foreach($js->parts as $w){
            if($w === $part_id){
                $trovato=true;
            }
        }
        $this->assertTrue(!$trovato);

    }
}
