<?php

namespace PipeLhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use PipeLhd\Utils\ResponseHttp;

class CheckScriptsMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        $projectConfig = $request->getAttribute('project_config');
        $deploymentsDir = ROOT_PATH . '/scripts/';
        
        $lifecycle = $projectConfig['lifecycle'];

        $missingScripts = [];

        foreach ($lifecycle as $phase => $scripts) {
            foreach ($scripts as $script) {
                $scriptPath = $deploymentsDir . $script;
                if (!file_exists($scriptPath)) {
                    $missingScripts[] = $scriptPath;
                }
            }
        }

        if (!empty($missingScripts)) {
            return ResponseHttp::generateJson((object)["error" => "Missing scripts", "missing_scripts" => $missingScripts], 400);
        }

        $response = $handler->handle($request);
        return $response;
    }
}