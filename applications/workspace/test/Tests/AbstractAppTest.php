<?php

namespace Tests;

use JsonSchema\Validator;

trait AbstractAppTest
{
    public function createApplication()
    {
        // print_r("DBG creao app");
        //$app = require_once __DIR__.'/../../src/App.php';
        $app = require __DIR__.'/../../src/App.php';
        $app['debug'] = true;
        $app['session.test'] = true;
        #unset($app['exception_handler']);
        return $app;
    }

    public function logIn($client)
    {
        $client->request(
            'POST',
            '/api/v1/security/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"authMode":"Email","email":"ugo.ugo@ugo.it","name":"ugo","surname":"ugo","password":"cane"}');
        return $client;
    }
    public function logIn2($client)
    {
        $client->request(
            'POST',
            '/api/v1/security/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"authMode":"Email","email":"ugo2","name":"ugo2","surname":"ugo2","password":"cane"}');
        return $client;
    }

    /**
     * @param string $schema
     */
    public function askValidation($data, $schema)
    {
        $validator = new \JsonSchema\Validator;
        $js_schema = (object)['$ref' => 'file://'.$schema];
        $validator->check(json_decode($data), $js_schema);
        return $validator;
    }

    public function evalValidation($validator) {
        $assert = false;
        if ($validator->isValid()) {
            echo "The supplied JSON validates against the schema.\n";
            $assert = true;
        }else {
            echo "JSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                echo "[{$error['property']}] {$error['message']}\n";
            }
            $assert = false;
        }
        return $assert;
    }
}
