<?php

namespace PipeLhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use PipeLhd\Utils\ResponseHttp;

class CheckDeploymentProjectMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        try {
            $deploymentsFile = ROOT_PATH . '/deployments/deployments.php';
            $deploymentsDir = ROOT_PATH . '/deployments/';

            $projectName = $request->getAttribute('project');

            $projects = include $deploymentsFile;

            if (!array_key_exists($projectName, $projects)) {
                return ResponseHttp::generateJson((object)["error" => "Project is not registered"], 400);
            }

            $projectFilePath = $deploymentsDir . $projects[$projectName];

            if (!file_exists($projectFilePath)) {
                return ResponseHttp::generateJson((object)["error" => "Project configuration file does not exist"], 400);
            }

            $projectConfig = include $projectFilePath;

            if (!is_array($projectConfig)) {
                return ResponseHttp::generateJson((object)["error" => "Invalid project configuration"], 400);
            }

            $request = $request->withAttribute('project_config', $projectConfig);

            $response = $handler->handle($request);

            return $response;

        } catch (\Exception $e) {
            return ResponseHttp::generateJson((object)["error" => $e->getMessage()], 400);
        }
    }
}