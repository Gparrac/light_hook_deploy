<?php

namespace Lhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Lhd\Utils\ResponseHttp;

class KeyMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        return ResponseHttp::generateJson((object)["hello" => "holi"], 200);

        $response = $handler->handle($request);
        
        return $response;
    }
}