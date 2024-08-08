<?php

namespace PipeLhd\Config;

use DI\ContainerBuilder;
use PipeLhd\Controllers\DeployController;
use PipeLhd\Controllers\RollbackController;
use PipeLhd\Controllers\AllInOneController;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        AllInOneController::class => function ($container) {
            return new AllInOneController(
                $container->get(DeployController::class),
                $container->get(RollbackController::class)
            );
        }
    ]);
};