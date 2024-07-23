<?php

use Slim\App;

use PipeLhd\Middlewares\KeyMiddleware;
use PipeLhd\Middlewares\RateLimitMiddleware;
use PipeLhd\Middlewares\CheckDeploymentProjectMiddleware;
use PipeLhd\Middlewares\CheckScriptsMiddleware;
use PipeLhd\Middlewares\CheckDirectoryMiddleware;

use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use PipeLhd\Controllers\DeployController;
use PipeLhd\Controllers\RollbackController;

return function (App $app) {
    $app->group('/v1', function (Group $group){
        $group->post('/deploy', DeployController::class);
        $group->post('/rollback', RollbackController::class);
    })
    ->add(CheckDirectoryMiddleware::class)
    ->add(CheckScriptsMiddleware::class)
    ->add(CheckDeploymentProjectMiddleware::class)
    ->add(KeyMiddleware::class)
    ->add(RateLimitMiddleware::class);
};