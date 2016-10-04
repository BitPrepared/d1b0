<?php

namespace Tests;

use JsonSchema\Constraints\Constraint;
use JsonSchema\SchemaStorage;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Validator;
use Prophecy\Argument;

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

    public function askValidation($data, $schema)
    {
        $validator = new \JsonSchema\Validator;
        $js_schema =  (object)['$ref' => 'file://' . $schema];
        $validator->check(json_decode($data), $js_schema);
        return $validator;
    }
}