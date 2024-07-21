<?php

use Slim\App;
use Lhd\Controllers\Controller;
use Lhd\Middlewares\CustomMiddleware;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group('/v1', function (Group $group){
        $group->get('/deploy', [Controller::class, 'example'])
              ->add(CustomMiddleware::class);
        $group->get('/rollback', [Controller::class, 'example']);
    })->add(CustomMiddleware::class);
};