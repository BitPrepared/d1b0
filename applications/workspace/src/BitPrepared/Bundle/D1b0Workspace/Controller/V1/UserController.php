<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Monolog\Logger;

class UserController implements ControllerProviderInterface
{

    private $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $factory = $app['controllers_factory'];
        # il mount point e' precedente e non serve prima
        $factory->get('/signup', array($this, 'signup'));
        $factory->get('/{id}', array($this, 'get'));
        return $factory;
    }

    public function get($id, Request $request)
    {
        $this->app['db']; //attivo il facade R
        $e = R::findAll('table', ' ORDER BY date DESC LIMIT 2');
        return "$id";
    }

    public function signup(Request $request)
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
