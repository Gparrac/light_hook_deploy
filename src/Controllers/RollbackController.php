<?php

namespace PipeLhd\Controllers;

use PipeLhd\Services\ScriptExecutor\ScriptWithVariables;
use PipeLhd\Services\LifecycleScriptService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PipeLhd\Utils\ResponseHttp;

class RollbackController
{
    private $lifecycleScriptService;

    public function __construct()
    {
        $this->lifecycleScriptService = new LifecycleScriptService(new ScriptWithVariables);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $projectConfig = $request->getAttribute('project_config');
        $deployVariables = $projectConfig['deploy_variables'] ?: [];
        $directory = $projectConfig['directory'];

        // Execute rollback scripts
        $rollbackResult = $this->lifecycleScriptService->executeScripts($projectConfig['lifecycle']['rollback'], $deployVariables, $directory);
        $responseData = [
            'rollback' => $rollbackResult
        ];

        if ($rollbackResult['status'] === 'error') {
            return ResponseHttp::generateJson((object)$responseData, 500);
        }

        // Execute post-deploy scripts
        $postDeployResult = $this->lifecycleScriptService->executeScripts($projectConfig['lifecycle']['post_deploy'], $deployVariables, $directory);
        $responseData['post_deploy'] = $postDeployResult;

        if ($postDeployResult['status'] === 'error') {
            return ResponseHttp::generateJson((object)$responseData, 500);
        }

        return ResponseHttp::generateJson((object)$responseData, 200);
    }
}