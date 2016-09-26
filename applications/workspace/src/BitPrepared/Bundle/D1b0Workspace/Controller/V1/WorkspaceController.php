<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Monolog\Logger;
use RedBeanPHP\Facade as R;

class WorkspaceController implements ControllerProviderInterface
{

    private $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $factory = $app['controllers_factory'];
        # il mount point e' precedente e non serve prima
        $this->app['db'];
        R::fancyDebug( TRUE );
        $factory->get('/', array($this, 'getWorkspaceList'));
        $factory->post('/', array($this, 'createWorkspace'));
        return $factory;
    }
    public function getSessionId(){
        $user_id=$this->app['session']->get('user')['id'];
        return $user_id;
    }
    public function getWorkspaceList(Request $request)
    {
        $user_id=$this->getSessionId();
        $workspaces =  R::getAll("SELECT ws.id,
                                          ws.title,
                                          ws.description,
                                          ws.environment,
                                          ws.completed
                                          FROM userworkspace AS uws
                                          LEFT JOIN workspace AS ws
                                          ON uws.workspace = ws.id
                                          WHERE uws.user = ?",[$user_id]);
        $list=[];
        foreach($workspaces as $ws){
            array_push($list,[
                "id"=>$ws['id'],
                "title"=>$ws['title'],
                "description"=>$ws['description'],
                "environment"=>$ws['environment'],
                "point"=>0,//TODO fare una view con i point già calcolati per il ws
                "completed"=>$ws['completed'],
            ]);
        }
        $headers = [];
        return JsonResponse::create($list, 200, $headers)->setSharedMaxAge(300);

    }
    public function createWorkspace(Request $request)
    {
        $user_id=$this->getSessionId();
        $counter=0;
        $data = json_decode($request->getContent(), true);
        //TODO validate json_decode
        $title=$data['title'];
        $description=$data['description'];
        $environment=$data['environment'];

        $patrol = $data['team']['patrol'];
        $unit = $data['team']['unit'];
        $group = $data['team']['group'];

        //save the workspace get id
        $ws = R::dispense("workspace");
            $ws->title=$title;
            $ws->description=$description;
            $ws->environment=$environment;
            $ws->completed=false;
            $ws->inserttime=date('Y-m-d H:i:s');
            $ws->lastupdatetime=date('Y-m-d H:i:s');
        $id = R::store($ws);

        //save the team
        $team = R::dispense("team");
            $team->workspace=$id;
            $team->patrol=$patrol;
            $team->unit=$unit;
            $team->group=$group;
        $team_id = R::store($team);

        //create a phantom part to add badge
        $part = R::dispense("part");
            $part->workspace=$id;
            $part->user=$user_id;
            $part->inserttime=date('Y-m-d H:i:s');
            $part->lastupdatetime=date('Y-m-d H:i:s');
            $part->totalpoint=0;
        $part_id = R::store($part);

        //add the badge to the project
        foreach($data['badges'] as $badge_id){
            //TODO insert those badge as first hidden post
            $pb = R::dispense("partbadge");
                $pb->badge=$badge_id;
                $pb->part=$part_id;
                $pb->inserttime=date('Y-m-d H:i:s');
            $tmp = R::store($pb);
        }

        //add the workspace created to the user as owner
        $usw = R::dispense("userworkspace");
            $usw->user=$user_id;
            $usw->workspace=$id;
            $usw->inserttime=date('Y-m-d H:i:s');
        R::store($usw);

        $res = ["id" => $id];
        $headers = [];
        return JsonResponse::create($res, 201, $headers)->setSharedMaxAge(300);
    }
}
