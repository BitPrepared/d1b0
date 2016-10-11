<?php

namespace Tests;

use Silex\WebTestCase;
use JsonSchema\Validator;

class WorkspaceCheckinTest extends PartTest
{
    use AbstractAppTest;


    public function testPostWorkspaceCheck(){
        $blob = $this->testPostWorkspacePart();
        $id = $blob[0];
        $part_id= $blob[1];

        print_r("FANFARA!");
        var_dump($blob);
        $client = $this->createClient();
        $client = $this->logIn2($client);
        $client->request(
          'POST',
          '/api/v1/workspace/'.$id.'/part/'.$part_id.'/checkin',
          [],
          [],
          ['CONTENT_TYPE' => 'application/json'],
          '');
        $response = $client->getResponse();
        $data = $client->getResponse()->getContent();
        print_r("ROBAGROSSA");
        var_dump($data);
        $js = json_decode($data);
        $points = $js->points;

        $this->assertTrue(is_numeric($points));  //TODO verificare che il numero sia adeguato ai punti attesi

        return [$id,$part_id];
    }

    public function testDeletePostWorkspaceCheck(){
        $blob = $this->testPostWorkspaceCheck();
        $id = $blob[0];
        $part_id= $blob[1];

        print_r("FANFARA!");
        var_dump($blob);
        $client = $this->createClient();
        $client = $this->logIn2($client);
        $client->request(
          'DELETE',
          '/api/v1/workspace/'.$id.'/part/'.$part_id.'/checkin',
          [],
          [],
          ['CONTENT_TYPE' => 'application/json'],
          '');
        $response = $client->getResponse();


        $this->assertEquals(204,$response->getStatusCode());  //TODO verificare che il dato non sia più presente sul server

        return [$id,$part_id];
    }
}
