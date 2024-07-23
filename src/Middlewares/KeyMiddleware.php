<?php

namespace PipeLhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use PipeLhd\Utils\ResponseHttp;

class KeyMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        if(strpos($request->getHeaderLine('Content-Type'), 'application/json') !== false) {
            $params = json_decode($request->getBody()->getContents(), true);
        } else {
            $params = $request->getParsedBody();
        }

        $projects = include ROOT_PATH . '/src/Config/keys.php';

        if (!isset($params['project']) || !array_key_exists($params['project'] , $projects)){
            return ResponseHttp::generateJson((object)["error" => "Project name does not exist"], 400);
        }

        if (!isset($params['password'])){
            return ResponseHttp::generateJson((object)['error' => 'Password is required'], 400);
        }

        $storedHash = $projects[$params['project']];

        if (!password_verify($params['password'], $storedHash)) {
            return ResponseHttp::generateJson((object)["error" => "Invalid credentials"], 403);
        }

        $request = $request->withAttribute('project', $params['project']);

        $response = $handler->handle($request);
        
        return $response;
    }
}
