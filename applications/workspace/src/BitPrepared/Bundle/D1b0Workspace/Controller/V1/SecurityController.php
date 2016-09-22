<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Monolog\Logger;
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
        return $factory;
    }

    public function login(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if($data === NULL){
            $headers=[];
            $response = JsonResponse::create($res, 403, $headers)->setSharedMaxAge(300);
            return $response;
        }

        $authMode = $data['authMode'];

        if($authMode === 'Email'){
            $email = $data['email'];
            $password = $data['password'];
            $name = $data['name'];
            $surname = $data['surname'];
            $user = R::findOne('user',"WHERE email = ? AND name = ? AND surname = ?",[$email,$name,$surname]);
            if($user->pwd === hash("sha256",$user->salt.$password)){
                //LOGGED IN!
                $this->app['session']->set('user', ['id' => $user->id]);
                $headers=[];
                $res = [
                        "token"=>"blablabla",//TODO CREATE token
                        "clientId"=>$user->id
                ];
                $response = JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
            }else{
                $headers=[];
                $res = [
                        "errore"=>"sbagliato password o user" //TODO roba
                ];
                $response = JsonResponse::create($res, 401, $headers)->setSharedMaxAge(300);
            }
        }
        else{
            //Facebook Redirect
        }
        return $response;// JsonResponse::create($output, 200, $headers)->setSharedMaxAge(300);
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
}
