<?php

namespace PipeLhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PipeLhd\Utils\ResponseHttp;
use PipeLhd\Config\ErrorCodes;

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
                $body = (string) $response->getBody();
                $this->logger->error('Error Response: ' . $response->getReasonPhrase() . ' - Status Code: ' . $statusCode . ' - Body: ' . $body);
            }
        } catch (\Throwable $e) {
            $this->logger->error('System Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            $response = ResponseHttp::generateJson(
                ["error" => $e->getMessage()],
                500,
                false,
                ErrorCodes::UNEXPECTED_ERROR
            );
        }
        return $response;
    }
}