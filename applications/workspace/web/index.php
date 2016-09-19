<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

use BitPrepared\Bundle\D1b0Workspace\Application\D1b0Application;
use BitPrepared\Bundle\D1b0Workspace\Controller\V1\D1b0Controller;
use BitPrepared\Bundle\D1b0Workspace\Controller\V1\StatusController;
use BitPrepared\Bundle\D1b0Workspace\Controller\V1\UserController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Provider\MonologServiceProvider;
use Ivoba\Silex\RedBeanServiceProvider;
use Carbon\Carbon;
use Monolog\Logger;
use RedBean_Facade as R;

// FIXME va messo nel php.ini
date_default_timezone_set('Europe/Rome');

$app = new D1b0Application();

// config developing (da portare fuori!)
$app['debug'] = true;
$app['log.level'] = Monolog\Logger::DEBUG; //ERROR in prod
$app['api.version'] = "v1";
$app['api.endpoint'] = "/api";
define('ROOT_PATH', __DIR__); //solo in dev va bene
// @remember: https://{enviroment}.{domain}/{contextPath}/api/v1

// manca il contesto , FIXME: da pensare le ACL di haproxy come saranno da qui si decide come usare come mountpoint
$baseUrl = ''.$app['api.endpoint'].'/'.$app['api.version'];

// @see: http://silex.sensiolabs.org/doc/providers/monolog.html
$app->register(new MonologServiceProvider(), array(
    "monolog.logfile" => ROOT_PATH . "/storage/logs/development_" . Carbon::now('Europe/Rome')->format("Y-m-d") . ".log",
    "monolog.level" => $app["log.level"],
    "monolog.name" => "application"
));

// @see: https://github.com/ivoba/redbean-service-provider
//'mysql:host=localhost;dbname=mydatabase', 'user', 'password'
$app->register(new RedBeanServiceProvider(), array('db.options' => array( 'dsn' => 'sqlite:'.ROOT_PATH.'../../database/workspace.sqlite' )));

// production (X-Forwarded-For*)
//Request::setTrustedProxies(array($ip));
Request::enableHttpMethodParameterOverride();

//handling CORS preflight request
$app->before(function (Request $request) {
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
$app->after(function (Request $request, Response $response) {
    $response->headers->set("Access-Control-Allow-Origin", "*");
    $response->headers->set("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
});

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    // this handler will handle \Exception
    $app['monolog']->addError($e->getMessage());
    $app['monolog']->addError($e->getTraceAsString());
    return new Response('Error', 404 /* ignored */, array('X-Status-Code' => 200));
});


// Controller
$app->mount('/api/v1', new D1b0Controller());
$app->mount('/api/v1/status', new StatusController());
$app->mount('/api/v1/user', new UserController());

$app->run();


// @SERVER: php -S 127.0.0.1:8080 -t web/
// @TEST:  http://127.0.0.1:8080/api/v1/status



// $blogPosts = [];
// $app->get('/sample/{id}', function ($id) use ($blogPosts) {
//
//     $app['db']; //attivo il facade R
//     $e = R::findAll('table', ' ORDER BY date DESC LIMIT 2');
//
//     // ...
//     // per ricordarsi di validare anche lo User-Agent in caso
//     // ...
//     if (!isset($blogPosts[$id])) {
//         $app->abort(404, "Post $id does not exist.");
//     }
// })
// ->assert('id', '\d+')
// ->when("request.headers.get('User-Agent') matches '/firefox/i'");
