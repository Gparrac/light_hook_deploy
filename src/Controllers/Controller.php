<?php

namespace PipeLhd\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Controller
{
    public function example(Request $request, Response $response, array $args): Response
    {
        $response->getBody()->write("Controller");
        return $response;
    }
}