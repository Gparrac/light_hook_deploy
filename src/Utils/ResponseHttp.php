<?php

namespace PipeLhd\Utils;

use Nyholm\Psr7\Response;

class ResponseHttp
{
    public static function generateJson(object $messageJson, int $statusCode): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode($messageJson));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}