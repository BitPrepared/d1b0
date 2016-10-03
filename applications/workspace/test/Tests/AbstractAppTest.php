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
        $app = require_once __DIR__.'/../../src/App.php';
        $app['debug'] = true;
         $app['session.test'] = true;
        #unset($app['exception_handler']);
        return $app;
    }

    public function askValidation($data,$schema){
        $validator = new \JsonSchema\Validator;
        $js_schema =  (object)['$ref' => 'file://' . $schema];
        $validator->check(json_decode($data), $js_schema);
        return $validator;
    }
}
