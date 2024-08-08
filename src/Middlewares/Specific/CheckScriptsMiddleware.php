<?php

namespace PipeLhd\Middlewares\Specific;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use PipeLhd\Utils\ResponseHttp;
use PipeLhd\Exceptions\MissingScriptsException;

class CheckScriptsMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        try {
            $projectConfig = $request->getAttribute('project_config');
            $deploymentsDir = DEPLOYMENT_PATH . 'scripts/';
            
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
                throw new MissingScriptsException("Missing scripts found: " . implode(', ', $missingScripts));
            }

            $response = $handler->handle($request);
        return $response;
        }  catch (MissingScriptsException $e) {
            return ResponseHttp::generateJson(
                ["error" => $e->getMessage()],
                400,
                false,
                $e->getCode()
            );
        }
        
    }
}