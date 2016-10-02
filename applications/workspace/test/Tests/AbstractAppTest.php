<?php

namespace Tests;

trait AbstractAppTest
{
    public function createApplication()
    {
        $app = require_once __DIR__.'/../../src/App.php';
        $app['debug'] = true;
        #unset($app['exception_handler']);
        return $app;
    }
}
