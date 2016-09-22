<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Monolog\Logger;
use RedBeanPHP\Facade as R;

class UserController implements ControllerProviderInterface
{

    private $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $factory = $app['controllers_factory'];
        # il mount point e' precedente e non serve prima
        $this->app['db'];
        R::fancyDebug( TRUE );
        $factory->post('/signup', array($this, 'signup'));
        $factory->get('/{id}', array($this, 'get'));
        return $factory;
    }

    public function get($id, Request $request)
    {
        $user = R::findAll('user', 'id = ?',["$id"]);
        $headers = [];
        var_dump($user);
        return JsonResponse::create($user, 200, $headers)->setSharedMaxAge(300);
    }

    public function signup(Request $request)
    {
        // $this->app['monolog']->addInfo(sprintf("Required '%s'.", 'status'));
        $this->app->log('log info', [], Logger::INFO); //grazie al traits <- da trasformare prima in app
        $data = json_decode($request->getContent(), true);
        //TODO: https://github.com/justinrainbow/json-schema VALIDARE LA ROBA IN INPUT

        $authMode = $data['authMode'];
        $id = -1;
        if($authMode === 'Email'){
            /*
            $user = R::dispense('user');
            $user->authMode=$data['authMode'];
            $user->name=$data['name'];
            $user->surname=$data['surname'];
            $user->surname=$data['surname'];*/
            try{
                $user = R::dispense('user');
                //$user->import($data);
                $size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
                $iv = mcrypt_create_iv($size, MCRYPT_DEV_RANDOM);
                $user->salt = $iv;
                $user->pwd = hash("sha256",$iv.$data['password']);
                $user->status = "checking";
                //$user->id="11";
                $user->name=$data['name'];
                $user->email=$data['email'];
                $user->surname=$data['surname'];
                $user->authmode=$data['authMode'];
                $user->inserttime=date('Y-m-d H:i:s');;
                $user->updatetime=date('Y-m-d G:i:s');;
                $id = R::store($user);
                $res = (object)["id" => $id];
            }catch(Exception $e){
                echo $e;
            }

        }else{

        }

        $headers = [];
        //TODO redirect to other page (/security/callback?authMode=Email)
        return JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
    }
}
