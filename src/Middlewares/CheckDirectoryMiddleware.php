<?php

namespace PipeLhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use PipeLhd\Utils\ResponseHttp;
use PipeLhd\Exceptions as CustomException;

class CheckDirectoryMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        try {
            $projectConfig = $request->getAttribute('project_config');
            
            if (!isset($projectConfig['directory'])) {
                throw new CustomException\DirectoryNotSpecifiedException("Directory not specified in project_config");
            }
    
            $directory = ROOT_PATH . '/deployments/' . $projectConfig['directory'];
    
            if (!is_dir($directory) || !is_readable($directory)) {
                throw new CustomException\DirectoryNotAccessibleException("Directory not accessible: " . $directory);
            }
    
            return $handler->handle($request);
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