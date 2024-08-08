<?php

namespace PipeLhd\Config;

use PipeLhd\Middlewares\Global\LogMiddleware;
use PipeLhd\Middlewares\Global\RateLimitMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(RateLimitMiddleware::class)
    ->add(new LogMiddleware(LoggerConfig::createLogger()));
};