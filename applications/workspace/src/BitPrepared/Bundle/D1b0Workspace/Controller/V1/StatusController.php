<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Monolog\Logger;

class StatusController implements ControllerProviderInterface
{

    private $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $factory = $app['controllers_factory'];
        # il mount point e' precedente e non serve prima
        $factory->get('/', array($this, 'status'));
        //$factory->get('/status', 'BitPrepared\Bundle\D1b0Workspace\Controller\V1\StatusController::status');
        return $factory;
    }

    public function status(Request $request)
    {

        // sample sintatticamente anche errato
        // $this->app['monolog']->addInfo(sprintf("Required '%s'.", 'status'));
        $this->app->log('log info', [], Logger::INFO); //grazie al traits <- da trasformare prima in app

        $data = array(
          "workspace" => "OK",
          "fileManager" => "OK",
          "externalLogin" => "OK"
        );

        $headers = [];

        return JsonResponse::create($data, 200, $headers)->setSharedMaxAge(300);
    }
}
