<?php

use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Lhd\Middlewares\LogMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Create the Slim application
$app = AppFactory::create();

// Configure the logger
$logFile = __DIR__ . '/../Logs/app.log';
$logger = new Logger('app');
$logger->pushHandler(new StreamHandler($logFile, Level::Debug));

// logger to the app's container
$app->getContainer()['logger'] = $logger;

// Global middleware for logging
$app->add(new LogMiddleware($logger));

// Error Middlewares
$displayErrorDetails = getenv('DISPLAY_ERROR_DETAILS') === 'true' || false;
$logErrors = getenv('LOG_ERRORS') === 'true' || true;
$logErrorDetails = getenv('LOG_ERROR_DETAILS') === 'true' || true;

$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);

// Configure the 404 error handler
$errorMiddleware->setErrorHandler(HttpNotFoundException::class, function (Request $request, $exception) use ($logger, $app) {
    $response = $app->getResponseFactory()->createResponse(404);
    $response->getBody()->write(file_get_contents(__DIR__ . '/resources/not_found.html'));

    // Log the 404 error
    $logger->warning('404 Not Found: ' . $request->getUri());

    return $response;
});

// Load routes
(require __DIR__ . '/../src/Config/routes.php')($app);

$app->run();