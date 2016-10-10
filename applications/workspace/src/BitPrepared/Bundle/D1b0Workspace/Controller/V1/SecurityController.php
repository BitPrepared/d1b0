<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use RedBeanPHP\Facade as R;

class SecurityController implements ControllerProviderInterface
{

    private $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $factory = $app['controllers_factory'];
        # il mount point e' precedente e non serve prima
        $this->app['db'];
        //R::fancyDebug( TRUE );
        $factory->post('/login', array($this, 'login'));
        $factory->get('/logout', array($this, 'logout'));
        $factory->get('/confirm', array($this, 'confirm'));
        return $factory;
    }
    public function login(Request $request)
    {
        /*TODO remove this line in producton DBG DATA {"authMode":"Email","email":"ugo.ugo@ugo.it","name":"ugo","surname":"ugo","password":"cane"}*/
        $data = json_decode($request->getContent(), true);
        if ($data === NULL) {
            $headers = [];
            $response = JsonResponse::create($res, 403, $headers)->setSharedMaxAge(300);
            return $response;
        }

        $authMode = $data['authMode'];

        if ($authMode === 'Email') {
            $email = $data['email'];
            $password = $data['password'];
            $name = $data['name'];
            $surname = $data['surname'];
            $user = R::findOne('user', "WHERE email = ? AND name = ? AND surname = ?", [$email, $name, $surname]);
            if ($user->pwd === hash("sha256", $user->salt.$password)) {
                //LOGGED IN!
                $this->app['session']->set('user', ['id' => $user->id]);
                $headers = [];
                $res = [
                        "token"=>"blablabla", //TODO CREATE token
                        "clientId"=>$user->id
                ];
                $response = JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
            }else {
                $headers = [];
                $res = [
                        "errore"=>"sbagliato password o user" //TODO roba
                ];
                $response = JsonResponse::create($res, 401, $headers)->setSharedMaxAge(300);
            }
        }else {
            //Facebook Redirect
        }
        return $response; // JsonResponse::create($output, 200, $headers)->setSharedMaxAge(300);
    }
    public function logout(Request $request)
    {
        $this->app['session']->clear();
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        $response->setSharedMaxAge(300);
        return $response;
    }
    public function confirm(Request $request)
    {
            $confirmKey = $request->request->get('confirmKey');
            $verify = R::findOne('verify', "WHERE key = ?", [$confirmKey]);
            if (!$bean->id) {
                //TODO mettere un controllo agli IP che forzano le richieste di token falsi
                $response = "<html><head></head><body>Token non esistente!</body></html>";
            }else {
                if (strtotime($verify->inserttime) < strtotime("-15 minutes")) {
                    $user = R::load('user', $verify->user);
                    $user->status = "enabled";
                    $user->updatetime = date('Y-m-d H:i:s');
                    $id = R::store($user);
                    $response = "<html><head></head><body>Account attivato complimenti!</body></html>";
                }else {
                    $response = "<html><head></head><body>Impossibile attivare account inserire mail e password per richiedere un nuovo token!</body></html>";
                }
            }
            return $response;
    }
}
