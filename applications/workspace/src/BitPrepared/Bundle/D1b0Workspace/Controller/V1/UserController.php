<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Monolog\Logger;
use RedBeanPHP\Facade as R;
use BitPrepared\Bundle\D1b0Workspace\Exception\UnauthorizedException;

class UserController implements ControllerProviderInterface
{

    private $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $factory = $app['controllers_factory'];
        # il mount point e' precedente e non serve prima
        $this->app['db'];
        //R::fancyDebug( TRUE );
        $factory->post('/signup', array($this, 'signup'));
        $factory->get('/{id}', array($this, 'get'))->before([$this, 'isSession']);
        $factory->post('/{id}/badge', array($this, 'postBadge'))->before([$this, 'isSession']);
        $factory->get('/{id}/badge/{id_badge}', array($this, 'getBadge'))->before([$this, 'isSession']);
        $factory->patch('/{id}/badge/{id_badge}/completed', array($this, 'markBadgeAsCompleted'))->before([$this, 'isSession']);
        $factory->delete('/{id}/badge/{id_badge}', array($this, 'deleteUserBadge'))->before([$this, 'isSession']);
        $factory->get('/{id}/ticket', array($this, 'getTicket'))->before([$this, 'isSession']);
        return $factory;
    }

    public function isSession(Request $request, Application $app) {
            if ($this->app['session']->has('user') !== true) {
                throw new UnauthorizedException("errore", 1);
            }
    }

    public function get($id, Request $request)
    {
        $user = R::findOne('user', 'id = ?', ["$id"]);
        $headers = [];

        $output = [
            'name'=>$user->name,
            'surname'=>$user->surname,
            'authmode'=>$user->authmode,
            'skills'=>'',
        ];

        $badges = R::findAll('userbadgecomplete', 'WHERE user = ?', [$id]);
        $badgeList = [];
        foreach ($badges as $badge) {
            array_push($badgeList,
                [
                    'badge'=>[
                        'id'=>intval($badge['id']),
                        'name'=>$badge['name'],
                        'description'=>$badge['description'],
                        'img'=>$badge['img']
                    ],
                    'clove'=>intval($badge['clove']),
                    'completed'=>boolval($badge['completed'])
                ]
            );
        }
        $output['skills'] = $badgeList;
        return JsonResponse::create($output, 200, $headers)->setSharedMaxAge(300);
    }

    public function signup(Request $request)
    {
        //TODO ricordarsi di salvare in log il login ^^
        // $this->app['monolog']->addInfo(sprintf("Required '%s'.", 'status'));
        $this->app->log('log info', [], Logger::INFO); //grazie al traits <- da trasformare prima in app
        $data = json_decode($request->getContent(), true);
        //TODO: https://github.com/justinrainbow/json-schema VALIDARE LA ROBA IN INPUT

        $authMode = $data['authMode'];
        $id = -1;
        if ($authMode === 'Email') {
            /*
            $user = R::dispense('user');
            $user->authMode=$data['authMode'];
            $user->name=$data['name'];
            $user->surname=$data['surname'];
            $user->surname=$data['surname'];*/
            try {
                $user = R::dispense('user');
                //$user->import($data);
                $size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
                $iv = mcrypt_create_iv($size, MCRYPT_DEV_RANDOM);
                $user->salt = $iv;
                $user->pwd = hash("sha256", $iv.$data['password']);
                $user->status = "checking";
                //$user->id="11";
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->surname = $data['surname'];
                $user->authmode = $data['authMode'];
                $user->inserttime = date('Y-m-d H:i:s');
                $user->updatetime = date('Y-m-d G:i:s');
                $id = R::store($user);
                $res = (object)["id" => $id];
            }catch (Exception $e) {
                echo $e;
            }

        }else {

        }

        $headers = [];
        //TODO redirect to other page (/security/callback?authMode=Email)
        return JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
    }

    public function postBadge($id, Request $request)
    {
        //TODO valiadre id in funzione della sessione utente (altrimenti chiunque aggiunge badge a chiunque)
        $data = json_decode($request->getContent(), true);

        $userbadge = R::dispense('userbadge');
        $userbadge->user = $id;
        $userbadge->badge = $data['id'];
        $userbadge->inserttime = date('Y-m-d H:i:s');
        $userbadge->updatetime = date('Y-m-d H:i:s');
        $id = R::store($userbadge);

        $res = (object)["id" => $id];
        $headers = [];
        return JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
    }

    public function getBadge($id, $id_badge, Request $request)
    {
        $badge = R::findOne('userbadgecomplete', 'WHERE user = ? AND badge = ?', [$id, $id_badge]);
        $res = [
                    'badge'=>[
                        'id'=>$badge['badge'],
                        'name'=>$badge['name'],
                        'description'=>$badge['description'],
                        'img'=>$badge['img']
                    ],
                    'clove'=>$badge['clove'],
                    'completed'=>$badge['completed']
                ];
        $headers = [];
        return JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
    }
    public function markBadgeAsCompleted($id, $id_badge, Request $request) {
        $userbadge = R::load('userbadge', $id_badge);
        $userbadge->user = $id;
        $userbadge->badge = $id_badge;
        $userbadge->updatetime = date('Y-m-d H:i:s');
        $userbadge->completed = 1;
        $id = R::store($userbadge);
        $res = (object)["id" => $id];
        $headers = [];
        return JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
    }
    public function deleteUserBadge($id, $id_badge, Request $request) {
        $userbadge = R::load('userbadge', $id_badge);
        $userbadge->deleted = 1;
        $userbadge->updatetime = date('Y-m-d H:i:s');
        $id = R::store($userbadge);
        $headers = [];
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        $response->setSharedMaxAge(300);
        return $response;
    }
    public function getTicket($id, Request $request) {
        $ticketRaw = R::findAll('ticket', 'WHERE user = ? AND (NOT status = "closed")', [$id]);

        $tickets = [];
        foreach ($ticketRaw as $ticket) {
            array_push($tickets, [
                "id"=>$ticket['id'],
                "message"=>$ticket['message'],
                "url"=>$ticket['url'],
                "priority"=>$ticket['priority']
            ]);
        }

        $headers = [];
        return JsonResponse::create($tickets, 200, $headers)->setSharedMaxAge(300);
    }
}
