<?php

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use DI\ContainerBuilder;

define('ROOT_PATH', dirname(__DIR__, 1));
define('DEPLOYMENT_PATH', ROOT_PATH . '/deployments/');

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$maxExecutionTime = $_ENV['MAX_EXECUTION_TIME'] ?? 30; // Default to 30 seconds
ini_set('max_execution_time', $maxExecutionTime);

//Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// // Set up providers
$providers = require ROOT_PATH . '/src/Config/providers.php';
$providers($containerBuilder);

// Build Container instance
$container = $containerBuilder->build();

// Create the Slim application
AppFactory::setContainer($container);
$app = AppFactory::create();

// Load Middlewares
$middleware = require ROOT_PATH. '/src/Config/middlewares.php';
$middleware($app);

// Load Routes
$routes = require ROOT_PATH. '/src/Config/routes.php';
$routes($app);

// Error Middlewares
$displayErrorDetails = $_ENV['DISPLAY_ERROR_DETAILS'] === 'true' || false;
$logErrors = $_ENV['LOG_ERRORS'] === 'true' || true;
$logErrorDetails = $_ENV['LOG_ERROR_DETAILS'] === 'true' || true;

$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);

$app->run();