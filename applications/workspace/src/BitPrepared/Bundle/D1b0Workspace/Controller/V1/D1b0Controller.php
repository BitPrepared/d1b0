<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class D1b0Controller implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        # il mount point e' precedente e non serve prima
        $factory->get('/', 'BitPrepared\Bundle\D1b0Workspace\Controller\V1\D1b0Controller::home');
        return $factory;
    }

    public function home()
    {
        return 'No basic route available';
    }
}
