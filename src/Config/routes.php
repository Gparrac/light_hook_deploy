<?php

namespace PipeLhd\Config;

use Slim\App;

use PipeLhd\Middlewares\Specific\KeyMiddleware;
use PipeLhd\Middlewares\Specific\CheckDeploymentProjectMiddleware;
use PipeLhd\Middlewares\Specific\CheckScriptsMiddleware;
use PipeLhd\Middlewares\Specific\CheckDirectoryMiddleware;
use PipeLhd\Middlewares\Specific\VerifyWebhookTokenMiddleware;

use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use PipeLhd\Controllers\DeployController;
use PipeLhd\Controllers\RollbackController;
use PipeLhd\Controllers\AllInOneController;

return function (App $app) {
    $app->group('/lhd', function (Group $group){
        $group->group('', function (Group $group){
            $group->post('/deploy', DeployController::class);
            $group->post('/rollback', RollbackController::class);
            $group->post('/all-in-one', AllInOneController::class);
        })
        ->add(CheckDirectoryMiddleware::class)
        ->add(CheckScriptsMiddleware::class)
        ->add(CheckDeploymentProjectMiddleware::class)
        ->add(KeyMiddleware::class);

        $group->group('/git', function (Group $group){
            $group->post('/all-in-one', AllInOneController::class);
        })
        ->add(CheckDirectoryMiddleware::class)
        ->add(CheckScriptsMiddleware::class)
        ->add(CheckDeploymentProjectMiddleware::class)
        ->add(VerifyWebhookTokenMiddleware::class);
    });
};