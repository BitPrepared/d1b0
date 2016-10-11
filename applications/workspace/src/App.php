<?php

use BitPrepared\Bundle\D1b0Workspace\Application\D1b0Application;
use BitPrepared\Bundle\D1b0Workspace\Controller\V1\D1b0Controller;
use BitPrepared\Bundle\D1b0Workspace\Controller\V1\StatusController;
use BitPrepared\Bundle\D1b0Workspace\Controller\V1\UserController;
use BitPrepared\Bundle\D1b0Workspace\Controller\V1\SecurityController;
use BitPrepared\Bundle\D1b0Workspace\Controller\V1\WorkspaceController;
use BitPrepared\Bundle\D1b0Workspace\Controller\V1\BadgeController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Provider\MonologServiceProvider;
use Ivoba\Silex\RedBeanServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Carbon\Carbon;
use Monolog\Logger;

// FIXME va messo nel php.ini
date_default_timezone_set('Europe/Rome');

$app = new D1b0Application();

// config developing (da portare fuori!)
$app['debug'] = $config['debug'];
$app['api.version'] = "v1";
$app['api.endpoint'] = "/api";
//solo in dev va bene
// @remember: https://{enviroment}.{domain}/{contextPath}/api/v1

// manca il contesto , FIXME: da pensare le ACL di haproxy come saranno da qui si decide come usare come mountpoint
$baseUrl = ''.$app['api.endpoint'].'/'.$app['api.version'];

// @see: http://silex.sensiolabs.org/doc/providers/monolog.html
$app->register(new MonologServiceProvider(), $config['logs']);

// @see: https://github.com/ivoba/redbean-service-provider
//'mysql:host=localhost;dbname=mydatabase', 'user', 'password'

if($config['database']['type']==='sqlite'){
    $app->register(new RedBeanServiceProvider(), array('db.options' => array('dsn' => 'sqlite:'.$config['database']['host'])));
}
if($config['database']['type']==='mysql'){
    $app->register(new RedBeanServiceProvider(), ['db.options' =>
                                                    [
                                                        'dsn' => 'mysql:'.$config['database']['host'].';dbname='.$config['database']['dbname'],
                                                        'user'=>$config['database']['username'],
                                                        'password'=>$config['database']['password']
                                                    ]
                                                ]);
}
$app->register(new SessionServiceProvider());


// production (X-Forwarded-For*)
//Request::setTrustedProxies(array($ip));
Request::enableHttpMethodParameterOverride();

//handling CORS preflight request
$app->before(function(Request $request) {
    if ($request->getMethod() === "OPTIONS") {
        $response = new Response();
        $response->headers->set("Access-Control-Allow-Origin", "*");
        $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,OPTIONS,DELETE");
        $response->headers->set("Access-Control-Allow-Headers", "Content-Type");
        $response->setStatusCode(200);
        return $response->send();
    }
}, Application::EARLY_EVENT);

//handling CORS respons with right headers
$app->after(function(Request $request, Response $response) {
    $response->headers->set("Access-Control-Allow-Origin", "*");
    $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
});

$app->error(function(\Exception $e, Request $request, $code) use ($app) {
    // this handler will handle \Exception
    $app['monolog']->addError($e->getMessage());
    $app['monolog']->addError($e->getTraceAsString());
    return new Response('Error', 404 /* ignored */, array('X-Status-Code' => 200));
});


// Controller
$app->mount('/api/v1', new D1b0Controller());
$app->mount('/api/v1/status', new StatusController());
$app->mount('/api/v1/user', new UserController());
$app->mount('/api/v1/security', new SecurityController());
$app->mount('/api/v1/workspace', new WorkspaceController());
$app->mount('/api/v1/badge', new BadgeController());

return $app;
