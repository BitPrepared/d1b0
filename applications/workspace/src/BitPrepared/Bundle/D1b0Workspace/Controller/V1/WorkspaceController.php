<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use RedBeanPHP\Facade as R;

class WorkspaceController implements ControllerProviderInterface
{

    public $POINT_FOR_USING_A_CONQUERED_BADGE = 200;
    public $POINT_FOR_USING_A_BADGE = 100;
    public $POINT_DEFAULT = 50;
    public $DATE_FORMAT = 'Y-m-d\TH:i:s\Z';
    private $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $factory = $app['controllers_factory'];
        # il mount point e' precedente e non serve prima
        $this->app['db'];
        R::fancyDebug(TRUE);
        $factory->get('/', array($this, 'getWorkspaceList'));
        $factory->post('/', array($this, 'createWorkspace'));
        $factory->get('/{id}', array($this, 'getWorkspace'));
        $factory->put('/{id}', array($this, 'putWorkspace'));
        $factory->delete('/{id}', array($this, 'deleteWorkspace'));
        $factory->get('/{id}/share', array($this, 'share'));
        $factory->post('/join', array($this, 'join'));
        $factory->post('/{id}/part', array($this, 'postPart'));
        $factory->get('/{id}/part/{part_id}', array($this, 'getPart'));
        $factory->put('/{id}/part/{part_id}', array($this, 'putPart'));
        $factory->delete('/{id}/part/{part_id}', array($this, 'deletePart'));
        $factory->post('/{id}/part/{part_id}/checkin', array($this, 'checkin'));
        $factory->delete('/{id}/part/{part_id}/checkin', array($this, 'deleteCheckin'));
        return $factory;
    }
    public function getSessionId() {
        $user_id = $this->app['session']->get('user')['id'];
        return $user_id;
    }
    public function getWorkspaceList(Request $request)
    {
        //print_r("sono qui");
        $user_id = $this->getSessionId();
        $workspaces = R::getAll("SELECT ws.id,
                                          ws.title,
                                          ws.description,
                                          ws.environment,
                                          ws.completed
                                          FROM userworkspace AS uws
                                          LEFT JOIN workspace AS ws
                                          ON uws.workspace = ws.id
                                          WHERE uws.user = ?",[$user_id]);
        $list = [];
        foreach ($workspaces as $ws) {
            array_push($list, [
                "id"=>intval($ws['id']),
                "title"=>$ws['title'],
                "description"=>$ws['description'],
                "environment"=>intval($ws['environment']),
                "point"=>0, //TODO fare una view con i point già calcolati per il ws
                "completed"=>boolval($ws['completed']),
            ]);
        }
        $headers = [];
        return JsonResponse::create($list, 200, $headers)->setSharedMaxAge(300);

    }
    public function createWorkspace(Request $request)
    {
        $user_id = $this->getSessionId();
        $data = json_decode($request->getContent(), true);
        //TODO validate json_decode
        $title = $data['title'];
        $description = $data['description'];
        $environment = $data['environment'];

        $patrol = $data['team']['patrol'];
        $unit = $data['team']['unit'];
        $group = $data['team']['group'];

        //save the workspace get id
        $ws = R::dispense("workspace");
            $ws->title = $title;
            $ws->description = $description;
            $ws->environment = $environment;
            $ws->completed = false;
            $ws->inserttime = date($this->DATE_FORMAT);
            $ws->lastupdatetime = date($this->DATE_FORMAT);
        $id = R::store($ws);

        //save the team
        $team = R::dispense("team");
            $team->workspace = $id;
            $team->patrol = $patrol;
            $team->unit = $unit;
            $team->group = $group;
        $team_id = R::store($team);

        //create a phantom part to add badge
        $part = R::dispense("part");
            $part->workspace = $id;
            $part->user = $user_id;
            $part->inserttime = date($this->DATE_FORMAT);
            $part->lastupdatetime = date($this->DATE_FORMAT);
            $part->totalpoint = 0;
            $part->deleted = false;
        $part_id = R::store($part);

        //add the badge to the project
        foreach ($data['badges'] as $badge_id) {
            $pb = R::dispense("partbadge");
                $pb->badge = $badge_id;
                $pb->part = $part_id;
                $pb->inserttime = date($this->DATE_FORMAT);
            $tmp = R::store($pb);
        }

        //add the workspace created to the user as owner
        $usw = R::dispense("userworkspace");
            $usw->user = $user_id;
            $usw->workspace = $id;
            $usw->inserttime = date($this->DATE_FORMAT);
        R::store($usw);

        $res = ["id" => $id];
        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);
    }

    public function putWorkspace($id, Request $request)
    {
        $user_id = $this->getSessionId();
        $data = json_decode($request->getContent(), true);
        //TODO validate json_decode

        $title = $data['title'];
        $description = $data['description'];
        $environment = $data['environment'];

        $patrol = $data['team']['patrol'];
        $unit = $data['team']['unit'];
        $group = $data['team']['group'];

        $ws = R::load("workspace", intval($id));
            $ws->title = $title;
            $ws->description = $description;
            $ws->environment = $environment;
            $ws->completed = false;
            $ws->lastupdatetime = date($this->DATE_FORMAT);
        $id = R::store($ws);

        //save the team
        $team = R::findOne("team", "workspace = ?", [$id]);
            $team->patrol = $patrol;
            $team->unit = $unit;
            $team->group = $group;
        $team_id = R::store($team);

        //TODO WE DELIBERATLY IGNORE ANY CHANGE IN BADGES AND PARTS, THEY MUST NOT BE EDITED HERE!!!! AND IF YOU DID WE DONT CARE!


        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        $response->setSharedMaxAge(300);
    }

    public function getWorkspace($id, Request $request) {
        $user_id = $this->getSessionId();
        //TODO controllare che l'utente abbia diritto a vedere questo workspace

        $workspace = R::findOne("workspace", "id = ?", [$id]);
        $team = R::findOne("team", "workspace = ?", [$id]);
        $part = R::findAll("part", "workspace = ? AND DELETED = 0", [$id]);

        $badges = R::findAll("workspacebadge", "workspace = ?", [$id]); //TODO controllare i deleted

        $l_part = [];
        foreach ($part as $p) {
            array_push($l_part, intval($p['id']));
        }
        $l_badges = [];
        foreach ($badges as $b) {
            array_push($l_badges, intval($b['badge']));
        }

        $res = [
            'id'=> intval($workspace['id']),
            'title'=> $workspace['title'],
            'description'=> $workspace['description'],
            'environment'=> intval($workspace['environment']),
            'completed'=> $workspace['completed'],
            'parts'=>$l_part,
            'badges'=>$l_badges,
            'team'=>[
                'patrol'=>$team['patrol'],
                'unit'=>$team['unit'],
                'group'=>$team['group']
            ]
        ];
        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);
    }

    public function deleteWorkspace($id, Request $request)
    {
        //Disassocia un utente da un workspace
        $user_id = $this->getSessionId();

        $wp = R::findOne("userworkspace", "workspace = ? AND user = ?", [$id, $user_id]);
        R::trash($wp);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        $response->setSharedMaxAge(300);
    }

    public function share($id, Request $request) {
        $generatedKey = hash("sha256", (mt_rand(10000, 99999).time().$id));
        //TODO verificare documentazione realtiva sulla reale entropia generata da questo sistema
        $user_id = $this->getSessionId();
        $share = R::dispense("share");
            $share->user = $user_id;
            $share->workspace = $id;
            $share->key = $generatedKey;
            $share->inserttime = date($this->DATE_FORMAT);
        $share_id = R::store($share);

        $date = new \DateTime();
        date_add($date, date_interval_create_from_date_string('15 minutes'));

        $res = [
            "id"=>$share_id,
            "key"=>$generatedKey,
            "expire"=>$date->format($this->DATE_FORMAT)
        ];

        $headers = [];
        return JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
    }

    public function join(Request $request) {


        $headers = [];
        $response = JsonResponse::create(["message"=>"No key found"], 400, $headers)->setSharedMaxAge(300);

        //TODO verificare documentazione realtiva sulla reale entropia generata da questo sistema
        $user_id = $this->getSessionId();
        $data = json_decode($request->getContent(), true);

        $key = $data['key'];

        $share = R::findOne("share", "key = ?", [$key]);
        echo $share->inserttime;
        if ($share !== NULL) {
            $date = new \DateTime();
            date_sub($date, date_interval_create_from_date_string('15 minutes'));

            $wp_id = $share['workspace'];

            $dateOld = new \DateTime($share->inserttime);
            if ($dateOld > $date) {
                $usw = R::dispense("userworkspace");
                    $usw->user = $user_id;
                    $usw->workspace = $wp_id;
                    $usw->inserttime = date($this->DATE_FORMAT);
                R::store($usw);
                $headers = [];
                $response = JsonResponse::create(["id"=>$wp_id], 200, $headers)->setSharedMaxAge(300);

            }else {
                $headers = [];
                $response = JsonResponse::create(["message"=>"Key no more valid"], 498, $headers)->setSharedMaxAge(300);
            }
        }

        return $response;
    }

    public function postPart($id, Request $request) {//TODO quando uno crea una parte bisognerebbe dire che lui c'era in quella parte
        $user_id = $this->getSessionId();

        $data = json_decode($request->getContent(), true);
        //var_dump($data);
        $part = R::dispense("part");
            $part->workspace = $id;
            $part->user = $user_id;
            $part->inserttime = date($this->DATE_FORMAT);
            $part->lastupdatetime = date($this->DATE_FORMAT);
            $part->totalpoint = 0;
            $part->deleted = false;
        $part_id = R::store($part);


        foreach ($data['part'] as $r) { //TODO va fixato nelle api
            $resource = R::dispense("resource");
                $resource->part = $part_id;
                $resource->inserttime = date($this->DATE_FORMAT);
                $resource->updatetime = date($this->DATE_FORMAT);
                $resource->type = $r['type'];
                $resource->ref = $r['ref'];
                $resource->hash = $r['hash'];
                $resource->available = false;
                $resource->totalpoint = 0;
            $resource_id = R::store($resource);
        }

        foreach ($data['badges'] as $badge_id) { //TODO va fixato nelle api
            $pb = R::dispense("partbadge");
                $pb->badge = $badge_id;
                $pb->part = $part_id;
                $pb->inserttime = date($this->DATE_FORMAT);
            $tmp = R::store($pb);
        }

        $res = ["id"=>$part_id];
        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);
    }

    public function getPart($id, $part_id, Request $request) {
        $user_id = $this->getSessionId();

        $data = json_decode($request->getContent(), true);

        $part = R::findOne("part", "id = ?", [$part_id]);

        $resource = R::findAll("resource", "part = ?", [$part_id]);

        $partecipants = R::findAll("cero", "part = ?", [$part_id]);

        $badges = R::findAll("partbadge", "part = ? AND deleted = 0", [$part_id]);

        $checked = false;
        $res = [
            "id"=>intval($part->id),
            "creation"=>$part->inserttime,
            "points"=>intval($part->points),
            "badges"=>[],
            "part"=>[],
            "partecipants"=>[]
        ];

        foreach ($badges as $b) {
            array_push($res['badges'], $b->id);
        }
        foreach ($resource as $r) {
            array_push($res['part'], [
                "type"=>$r->type,
                "hash"=>$r->hash,
                "ref"=>$r->ref
            ]);

        }
        foreach ($partecipants as $p) {
            array_push($res['partecipants'], $p->user); //TODO forse va usato l'id del c'ero e non l'id dell'utente
            if ($user_id == $r['id']) {
                            $checked = true;
            }
        }
        $res['present'] = true;

        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);
    }

    private function getPositionInArray($array, $id) {
        $count = 0;
        foreach ($array as $a) {
            if ($a->id === $id) {
                return $count;
            }
            $count = $count + 1;
        }
        return -1;
    }

    public function putPart($id, $part_id, Request $request) {
        $user_id = $this->getSessionId();

        $data = json_decode($request->getContent(), true);


        $part = R::load("part", $part_id);
            $part->workspace = $id;
            $part->user = $user_id;
            $part->lastupdatetime = date($this->DATE_FORMAT);
            $part->totalpoint = 0;
            $part->deleted = false;
        $part_id = R::store($part);

        $delete_res = R::findAll("resource", "WHERE part = ?", [$part_id]);

        foreach ($data['part'] as $r) { //TODO va fixato nelle api
            $resource = R::findOne("resource", "WHERE hash = ? AND deleted = 0", [$r['hash']]); //TODO BISOGNA FARE IL DIFF TRA QUELLE PRESENTI E QUELLE NON PRESENTI
                if ($resource == 0) {
                    $resource = R::dispense("resource");
                    $resource->available = false;
                    $resource->inserttime = date($this->DATE_FORMAT);
                }
                $resource->part = $part_id;
                $resource->updatetime = date($this->DATE_FORMAT);
                $resource->type = $r['type'];
                $resource->ref = $r['ref'];
                $resource->hash = $r['hash'];
                $resource->totalpoint = 0;
            $resource_id = R::store($resource);
            $rem_id = $this->getPositionInArray($delete_res, $resource_id);
            if ($rem_id != 0) {
                            array_splice($delete_res, $rem_id, 1);
            }
            //RIMUOVO GLI ELEMENTI CHE HO MODIFICATO
        }

        foreach ($delete_res as $d) {
            //RIMUOVO REALMENTE DAL DB LE COSE CHE HO LASCIATO FUORI DALLA PUT (PRESENTI NEL DB MA NON NELLA NUOVA VERSIONE ODIO LE PUT)
            $resource = R::load("resource", $d->id);
            $resource->deleted = true;
            R::store($resource);
        }

        $delete_badge = R::findAll("partbadge", "WHERE part = ? AND deleted = 0", [$part_id]);

        foreach ($data['badges'] as $badge_id) {
            $pb = R::load("partbadge", $badge_id);
                $pb->badge = $badge_id;
                $pb->part = $part_id;
            $tmp = R::store($pb);
            $rem_id = $this->getPositionInArray($delete_badge, $tmp);
            if ($rem_id != 0) {
                            array_splice($delete_badge, $rem_id, 1);
            }
            //RIMUOVO GLI ELEMENTI CHE HO MODIFICATO
        }

        foreach ($delete_badge as $d) {
            //RIMUOVO REALMENTE DAL DB LE COSE CHE HO LASCIATO FUORI DALLA PUT (PRESENTI NEL DB MA NON NELLA NUOVA VERSIONE ODIO LE PUT)
            $badge = R::load("partbadge", $d['id']); //FORSE RILOADARLI NON È NECESSARIO
            $badge->deleted = true;
            R::store($badge);
        }

        $res = ["id"=>$part_id];
        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);
    }

    public function deletePart($id, $part_id, Request $request) {
        $user_id = $this->getSessionId();
        $part = R::load("part", $part_id);
            $part->deleted = true;
        R::store($part);


        $delete_badge = R::findAll("partbadge", "WHERE part = ?", [$part_id]);
        foreach ($delete_badge as $d) {
            $badge = R::load("partbadge", [$d->id]); //FORSE RILOADARLI NON È NECESSARIO BASTA FARE $d->deleted=true; e store($d)
                $badge->deleted = true;
            R::store($badge);
        }

        //TODO soft delete resource!

        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        $response->setSharedMaxAge(300);
    }

    private function getPoint($badge_id, $badges) {
        foreach ($badges as $b) {
            if ($b->id === $badge_id) {
                if ($b->completed === True) {
                    echo "CASO 1;<BR />";
                    return $this->POINT_FOR_USING_A_CONQUERED_BADGE;
                }else {
                    echo "CASO 2;<BR />";
                    return $this->POINT_FOR_USING_A_BADGE;
                }
            }
        }
        echo "CASO 3;<BR />";
        return $this->POINT_DEFAULT;
    }
    public function checkin($id, $part_id, Request $request) {
        $user_id = $this->getSessionId();

        $badges = R::findAll("partbadge", "part = ? AND deleted = 0", [$part_id]);
        $u_badges = R::findAll("userbadge", "user = ? AND deleted = 0", [$user_id]);

        $point_earned = 0;
        foreach ($badges as $b) { //SE CI SONO DEI BADGE
            $point = $this->getPoint($b->id, $u_badges);
            if ($point != $this->POINT_DEFAULT) { //SE SEI IN CAMMINO PER QUEI BADGE O SE LI POSSIEDI GIÀ
                echo "PUNTI:".$point;
                $point_earned = $point_earned + $point;
                $pb = R::dispense("cero");
                    $pb->user = $user_id;
                    $pb->part = $part_id;
                    $pb->badge = $b->id;
                    $pb->inserttime = date($this->DATE_FORMAT);
                    $pb->points = $point;
                $tmp = R::store($pb);

                if ($point === $this->POINT_FOR_USING_A_BADGE) { //SE SEI IN CAMMINO MA NON LI HAI ANCORA RAGGIUNTI
                    $ubc = R::dispense("userbadgeclove");
                        $ubc->user = $user_id;
                        $ubc->badge = $b->id;
                        $ubc->part = $part_id;
                        $ubc->inserttime = date($this->DATE_FORMAT);
                    $tmp = R::store($ubc);
                }
            }
        }

        if ($point_earned <= 0) { //SE NON CI SONO BADGE O SE TU NON SEI IN CAMMINO PER NESSUNO DI LORO
            $pb = R::dispense("cero");
                $pb->user = $user_id;
                $pb->part = $part_id;
                $pb->inserttime = date($this->DATE_FORMAT);
                $pb->points = $this->POINT_DEFAULT;
            $tmp = R::store($pb);
        }
        $res = ["points"=>intval($point_earned)];
        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);

    }

    public function deleteCheckin($id, $part_id, Request $request) {
        $user_id = $this->getSessionId();

        $u_badges = R::findAll("userbadge", "user = ? AND part = ?", [$user_id, $part_id]);
        R::trashAll($u_badges);

        $cero = R::findAll("cero", "user = ? AND part = ?", [$user_id, $part_id]);
        R::trashAll($cero);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        $response->setSharedMaxAge(300);
        return $response;

    }
}
