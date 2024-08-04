<?php

namespace PipeLhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use PipeLhd\Utils\ResponseHttp;
use PipeLhd\Exceptions as CustomException;

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
                throw new CustomException\ProjectNotFoundException("Project is not registered");
            }

            $projectFilePath = $deploymentsDir . $projects[$projectName];

            if (!file_exists($projectFilePath)) {
                throw new CustomException\ConfigFileNotFoundException("Project configuration file does not exist");
            }

            $projectConfig = include $projectFilePath;

            if (!is_array($projectConfig)) {
                throw new CustomException\InvalidConfigException("Invalid project configuration");
            }

            $request = $request->withAttribute('project_config', $projectConfig);

            $response = $handler->handle($request);

            return $response;

        } catch (CustomException\DeploymentException $e) {
            return ResponseHttp::generateJson(
                ["error" => $e->getMessage()],
                400,
                false,
                $e->getCode()
            );
        }
    }
}