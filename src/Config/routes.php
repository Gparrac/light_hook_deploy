<?php

use Slim\App;
use PipeLhd\Controllers\Controller;
use PipeLhd\Middlewares\CustomMiddleware;
use PipeLhd\Middlewares\KeyMiddleware;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group('/v1', function (Group $group){
        $group->get('/deploy', [Controller::class, 'example']) // services pre_deploy, deploy, post_deploy
                // middleware deploy
                
              ->add(CustomMiddleware::class);
        $group->get('/rollback', [Controller::class, 'example']); //services rollback, post_deploy
                // pre_deploy y rollback middleware;
    })->add(KeyMiddleware::class);
};