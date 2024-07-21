<?php

namespace Lhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LogMiddleware
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        try {
            $response = $handler->handle($request);

            $statusCode = $response->getStatusCode();
            if ($statusCode != 200) {
                $this->logger->error('Error Response: ' . $response->getReasonPhrase() . ' - Status Code: ' . $statusCode);
            }
            } catch (\Throwable $e) {
                $this->logger->error('Error system: ' . $e->getMessage());
                return $response->withStatus(500);
            }

        return $response;
    }
}