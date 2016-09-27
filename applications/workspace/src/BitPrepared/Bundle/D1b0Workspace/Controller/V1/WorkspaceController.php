<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use RedBeanPHP\Facade as R;

class WorkspaceController implements ControllerProviderInterface
{

    public POINT_FOR_USING_A_CONQUERED_BADGE = 200;
    public POINT_FOR_USING_A_BADGE = 100;
    public POINT_DEFAULT = 50;

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
        $factory->get('/{id}/share', array($this, 'share'));
        $factory->post('/{id}/part', array($this, 'postPart'));
        $factory->get('/{id}/part/$id_part', array($this, 'getPart'));
        $factory->patch('/{id}/part/$id_part/checkin', array($this, 'checkin'));
        return $factory;
    }
    public function getSessionId() {
        $user_id = $this->app['session']->get('user')['id'];
        return $user_id;
    }
    public function getWorkspaceList(Request $request)
    {
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
                "id"=>$ws['id'],
                "title"=>$ws['title'],
                "description"=>$ws['description'],
                "environment"=>$ws['environment'],
                "point"=>0, //TODO fare una view con i point già calcolati per il ws
                "completed"=>$ws['completed'],
            ]);
        }
        $headers = [];
        return JsonResponse::create($list, 200, $headers)->setSharedMaxAge(300);

    }
    public function createWorkspace(Request $request)
    {
        $user_id = $this->getSessionId();
        $counter = 0;
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
            $ws->inserttime = date('Y-m-d H:i:s');
            $ws->lastupdatetime = date('Y-m-d H:i:s');
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
            $part->inserttime = date('Y-m-d H:i:s');
            $part->lastupdatetime = date('Y-m-d H:i:s');
            $part->totalpoint = 0;
        $part_id = R::store($part);

        //add the badge to the project
        foreach ($data['badges'] as $badge_id) {
            //TODO insert those badge as first hidden post
            $pb = R::dispense("partbadge");
                $pb->badge = $badge_id;
                $pb->part = $part_id;
                $pb->inserttime = date('Y-m-d H:i:s');
            $tmp = R::store($pb);
        }

        //add the workspace created to the user as owner
        $usw = R::dispense("userworkspace");
            $usw->user = $user_id;
            $usw->workspace = $id;
            $usw->inserttime = date('Y-m-d H:i:s');
        R::store($usw);

        $res = ["id" => $id];
        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);
    }

    public function getWorkspace($id, Request $request) {
        $user_id = $this->getSessionId();
        //TODO controllare che l'utente abbia diritto a vedere questo workspace

        $workspace = R::findOne("workspace", "id = ?", [$id]);
        $part = R::findAll("part", "workspace = ?", [$id]);

        $badges = R::findAll("workspacebadge", "workspace = ?", [$id]);

        $l_part = [];
        foreach ($part as $p) {
            array_push($l_part, intval($p['id']));
        }
        $l_badges = [];
        foreach ($badges as $b) {
            array_push($l_badges, intval($b['badge']));
        }

        $res = [
            'id'=> $workspace['id'],
            'title'=> $workspace['title'],
            'description'=> $workspace['description'],
            'environment'=> $workspace['environment'],
            'environment'=> $workspace['environment'],
            'completed'=> $workspace['completed'],
            'parts'=>$l_part,
            'badges'=>$l_badges
        ];
        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);
    }

    public function share($id, Request $request) {
        $generatedKey = hash("sha256", (mt_rand(10000, 99999).time().$id));
        //TODO verificare documentazione realtiva sulla reale entropia generata da questo sistema
        $user_id = $this->getSessionId();
        $share = R::dispense("share");
            $share->user = $user_id;
            $share->workspace = $id;
            $share->key = $generatedKey;
            $share->inserttime = date('Y-m-d H:i:s');
        $share_id = R::store($share);

        $date = new \DateTime();
        date_add($date, date_interval_create_from_date_string('15 minutes'));

        $res = [
            "id"=>$share_id,
            "key"=>$generatedKey,
            "expire"=>$date->format('Y-m-d H:i:s')
        ];

        $headers = [];
        return JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
    }

    public function postPart($id, Request $request) {
        $user_id = $this->getSessionId();

        $data = json_decode($request->getContent(), true);

        $part = R::dispense("part");
            $part->workspace = $id;
            $part->user = $user_id;
            $part->inserttime = date('Y-m-d H:i:s');
            $part->lastupdatetime = date('Y-m-d H:i:s');
            $part->totalpoint = 0;
        $part_id = R::store($part);

        foreach($data['part'] as $r){ //TODO va fixato nelle api
            if($r->type != "badge"){
                $resource = R::dispense("resource");
                    $resource->part = $part_id;
                    $resource->inserttime = date('Y-m-d H:i:s');
                    $resource->updatetime = date('Y-m-d H:i:s');
                    $resource->type = $r->type;
                    $resource->ref = $r->ref;
                    $resource->hash = $r->hash;
                    $resource->totalpoint = 0;
                $resource_id = R::store($resource);
            }
        }

        foreach($data['badges'] as $badge_id){ //TODO va fixato nelle api
            $pb = R::dispense("partbadge");
                $pb->badge = $badge_id;
                $pb->part = $part_id;
                $pb->inserttime = date('Y-m-d H:i:s');
            $tmp = R::store($pb);
        }

        $res = ["id"=>$part_id];
        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);
    }

    public function getPart($id,$part_id Request $request) {
        $user_id = $this->getSessionId();

        $data = json_decode($request->getContent(), true);

        $part = R::finOne("part","id = ?",[$part_id]);

        $resource = R::findAll("resource","part = ?"[$part_id]);

        $partecipants = R::findAll("cero","part = ?"[$part_id]);

        $badges = R::findAll("partbadge","part = ?"[$part_id]);

        $res= [
            "id"=>$part->id,
            "creation"=>$part->inserttime,
            "points"=>$part->points,
            "checked"=>$part->checked,
            "badges"=>[],
            "part"=>[],
            "partecipants"=>[]
        ];

        foreach($badges as $b){
            array_push($res['badges'],$b->id);
        }
        foreach($resource as $r){
            array_push($res['part'],[
                "type"=>$r->type,
                "hash"=>$r->hash,
                "ref"=>$r->ref
            ]);
        }
        foreach($partecipants as $p){
            array_push($res['partecipants'],$p->user);//TODO forse va usato l'id del c'ero e non l'id dell'utente
        }

        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);
    }
    private function getPoint($badge_id,$badges){
        foreach($badges as $b){
            if($b->id === $badge_id){
                if($b->completed === True)
                    return POINT_FOR_USING_A_CONQUERED_BADGE;
                else
                    return POINT_FOR_USING_A_BADGE;
            }
        }
        return POINT_DEFAULT;
    }
    public function checkin($id,$part_id Request $request) {
        $user_id = $this->getSessionId();

        $badges = R::findAll("partbadge","part = ?"[$part_id]);
        $u_badges = R::findAll("userbadge","user = ?"[$user_id]);

        foreach($badges as $b){
            $point = getPoint($b->id,$u_badges)
            $pb = R::dispense("cero");
                $pb->user = $user_id;
                $pb->part = $part_id;
                $pb->badge = $b->id;
                $pb->inserttime = date('Y-m-d H:i:s');
                $bp->points = $point;
            $tmp = R::store($pb);
            
            if($point === POINT_FOR_USING_A_BADGE){
                $ubc = R::dispense("userbadgeclove");
                    $ubc->user = $user_id;
                    $ubc->badge = $b->id;
                    $ubc->part = $part_id;
                    $ubc->inserttime = date('Y-m-d H:i:s');
                $tmp = R::store($ubc);
            }
        }


    }
}
