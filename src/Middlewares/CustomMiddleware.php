<?php

namespace Lhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class CustomMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        $response = $handler->handle($request);
        $response->getBody()->write('middleware 1');
        return $response;
    }
}