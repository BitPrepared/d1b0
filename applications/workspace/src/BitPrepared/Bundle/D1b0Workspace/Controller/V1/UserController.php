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
        $factory->post('/{id}/badge', array($this, 'postBadge'));
        return $factory;
    }

    public function get($id, Request $request)
    {
        $user = R::findOne('user', 'id = ?',["$id"]);
        $headers = [];

        $output = [
            'name'=>$user->name,
            'surname'=>$user->surname,
            'authmode'=>$user->authmode,
            'authmode'=>$user->authmode,
            'skills'=>'roba',
        ];

        $badges = R::getAll('SELECT
                            userbadge.id,
                            userbadge.badge,
                            userbadge.completed,
                            userbadge.inserttime,
                            badge.name,
                            badge.description,
                            badge.img,
                            COUNT(badge.id) AS clove
                            FROM userbadge
                            LEFT JOIN badge
                            ON userbadge.badge = badge.id
                            LEFT JOIN userbadgeclove
                            ON userbadgeclove.badge = badge.id
                            AND
                            userbadgeclove.user = userbadge.user
                            WHERE userbadge.user = ?
                            GROUP BY badge.id',[$id]);

        //TODO clove è sempre a uno perchè la COUNT di LEFT JOIN da sempre una entry! è un errore perchè da 1 anche a badge in cui il ragazzo non avrebbe clove
        //var_dump($badges);

        $badgeList=[];
        foreach( $badges as $badge){
            array_push($badgeList,
                [
                    'badge'=>[
                        'id'=>$badge['badge'],
                        'name'=>$badge['name'],
                        'description'=>$badge['description'],
                        'img'=>$badge['img']
                    ],
                    'clove'=>$badge['clove'],
                    'completed'=>$badge['completed']
                ]
            );
        }
        $output['skills']=$badgeList;
        return JsonResponse::create($output, 200, $headers)->setSharedMaxAge(300);
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
                $user->inserttime=date('Y-m-d H:i:s');
                $user->updatetime=date('Y-m-d G:i:s');
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

    public function postBadge($id,Request $request)
    {
        //TODO valiadre id in funzione della sessione utente (altrimenti chiunque aggiunge badge a chiunque)
        $data = json_decode($request->getContent(), true);

        echo "***".$data['id']."***";
        $userbadge = R::dispense('userbadge');
        $userbadge->user=$id;
        $userbadge->badge=$data['id'];
        $userbadge->inserttime=date('Y-m-d H:i:s');
        $id = R::store($userbadge);

        $res = (object)["id" => $id];
        $headers = [];
        return JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
    }
}
