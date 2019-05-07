<?php declare(strict_types=1);

namespace cyllenea\ssologin;

class Route
{
    public static function create()
    {
        $routeList = new \Nette\Application\Routers\RouteList('WitteLogin');
        $routeList[] = new \Nette\Application\Routers\Route('ssologin/<action>[/<id>]', 'SingleSignOn:default');
        return $routeList;
    }
}
