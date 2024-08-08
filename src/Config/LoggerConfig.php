<?php
namespace PipeLhd\Config;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Level;

class LoggerConfig
{
    public static function createLogger(): Logger
    {
        $logFile = ROOT_PATH . '/storage/logs/app.log';
        $logger = new Logger('app');
        $logger->pushHandler(new StreamHandler($logFile, Level::Debug));

        return $logger;
    }
}