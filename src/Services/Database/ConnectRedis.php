<?php

namespace PipeLhd\Services\Database;

use Predis\Client as RedisClient;

class ConnectRedis
{
    private static $instance = null;
    private $redis;

    private function __construct()
    {
        $options = [
            'scheme' => 'tcp',
            'host'   => $_ENV['REDIS_HOST'] ?: '127.0.0.1',
            'port'   => $_ENV['REDIS_PORT'] ?: 6379,
        ];

        $password = $_ENV['REDIS_PASSWORD'];
        if ($password) {
            $options['password'] = $password;
        }

        $this->redis = new RedisClient($options);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->redis;
    }
}
