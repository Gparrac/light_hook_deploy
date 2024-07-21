<?php

namespace Lhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Lhd\Utils\ResponseHttp;
use Psr\Log\LoggerInterface;

class KeyMiddleware
{
    private $projects =  include '../../Deployments/deployment_projects.php' ;
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        try {
            $params = $request->getParsedBody();
            if (!isset($params['project'])) {
                return ResponseHttp::generateJson((object)["message" => "project's name is required"], 400);
            }
            if (!array_key_exists($params['project'] , $this->projects)) {
                return ResponseHttp::generateJson((object)["message" => "invalid project's name"], 400);
            }
            
            $response = $handler->handle($request);
        } catch (\Throwable $e) {
            $this->logger->error('Error system: ' . $e->getMessage());
            return $response->withStatus(500);
        }
        return $response;
    }
}
