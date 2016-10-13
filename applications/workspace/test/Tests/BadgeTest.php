<?php

namespace Tests;

use Silex\WebTestCase;

class BadgeTest extends WebTestCase
{
    use AbstractAppTest;

    public function testGetBadgeSpecialita() {
        $schema = __DIR__.'/../../../../api/schemas/badgeList.json';
        $client = $this->createClient();
        $client = $this->logIn($client);

        $kind_of_badge = ['specialita', 'brevetti', 'eventi'];

        foreach ($kind_of_badge as $option) {

            $crawler = $client->request('GET', '/api/v1/badge/?filterBy='.$option);
            $response = $client->getResponse();

            //print_r($response);
            $data = $client->getResponse()->getContent();

            $validator = $this->askValidation($data, $schema);

            $assert = $this->evalValidation($validator);
            $this->assertTrue($assert);
        }
    }
}
