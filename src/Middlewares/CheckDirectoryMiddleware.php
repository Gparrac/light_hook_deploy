<?php

namespace PipeLhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use PipeLhd\Utils\ResponseHttp;

class CheckDirectoryMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        $projectConfig = $request->getAttribute('project_config');
        
        if (!isset($projectConfig['directory'])) {
            return ResponseHttp::generateJson((object)[
                "error" => "Directory not specified in project_config"
            ], 400);
        }

        $directory = $projectConfig['directory'];

        if (!is_dir($directory) || !is_readable($directory)) {
            return ResponseHttp::generateJson((object)[
                "error" => "Directory not accessible",
                "directory" => $directory
            ], 400);
        }

        return $handler->handle($request);
    }
}