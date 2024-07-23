<?php

namespace PipeLhd\Middlewares;

use PhpParser\Node\Expr\Cast\Object_;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use PipeLhd\Services\Database\ConnectRedis;
use PipeLhd\Utils\ResponseHttp;

class RateLimitMiddleware
{
    private $redis;
    private $maxAttempts;
    private $windowMinutes;

    public function __construct()
    {
        $this->redis = ConnectRedis::getInstance();
        $this->maxAttempts = getenv('MAX_ATTEMPTS_PER_DEPLOY') ?: 100;
        $this->windowMinutes = getenv('RATE_LIMIT_DEPLOY_MINUTES') ?: 60;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if ($_ENV['ENABLE_RATE_LIMIT'] != 'true') {
            return $handler->handle($request);
        }

        $ip = $request->getHeaderLine('X-Forwarded-For') ?: 
              $request->getHeaderLine('X-Real-IP') ?: 
              $request->getServerParams()['REMOTE_ADDR'];


        $key = "rate_limit:" . $ip;
        $attempts = $this->redis->get($key) ?: 0;

        if ($attempts >= $this->maxAttempts) {
            return ResponseHttp::generateJson((object)['error' => 'Too many Requests'], 429);
        }

        $this->redis->incr($key);
        if ($attempts == 0) {
            $this->redis->expire($key, $this->windowMinutes * 60);
        }

        return $handler->handle($request);
    }
}