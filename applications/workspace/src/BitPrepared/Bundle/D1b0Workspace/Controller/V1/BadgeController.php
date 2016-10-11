<?php

namespace BitPrepared\Bundle\D1b0Workspace\Controller\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use RedBeanPHP\Facade as R;

class BadgeController implements ControllerProviderInterface
{
    private $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $factory = $app['controllers_factory'];
        # il mount point e' precedente e non serve prima
        $this->app['db'];
        R::fancyDebug(TRUE);
        $factory->get('', array($this, 'get'));
        return $factory;
    }
    public function get(Request $request)
    {
        //$user_id = $this->getSessionId();
        $filter = $request->get('filterBy');
        echo $filter;

        $badge = R::findAll("badge", "type = ? AND enable = 1", [$filter]);

        $res = [];
        foreach ($badge as $b) {
            array_push($res, [
                "id"=>$b->id,
                "name"=>$b->name,
                "descritpion"=>$b->description,
                "img"=>$b->img
            ]);
        }

        $headers = [];
        return JsonResponse::create($res, 200, $headers)->setSharedMaxAge(300);
    }
}
