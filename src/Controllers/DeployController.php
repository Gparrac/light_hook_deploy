<?php

namespace PipeLhd\Controllers;

use PipeLhd\Services\LifecycleScriptService;
use PipeLhd\Services\ScriptExecutor\ScriptWithVariables;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PipeLhd\Utils\ResponseHttp;

class DeployController
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

        // Execute pre-deploy scripts
        $preDeployResult = $this->lifecycleScriptService->executeScripts($projectConfig['lifecycle']['pre_deploy'], $deployVariables, $directory);
        $responseData = [
            'pre_deploy' => $preDeployResult
        ];

        if ($preDeployResult['status'] === 'error') {
            return ResponseHttp::generateJson((object)$responseData, 500);
        }

        // Execute deploy scripts
        $deployResult = $this->lifecycleScriptService->executeScripts($projectConfig['lifecycle']['deploy'], $deployVariables, $directory);
        $responseData['deploy'] = $deployResult;

        if ($deployResult['status'] === 'error') {
            return ResponseHttp::generateJson((object)$responseData, 500);
        }

        // Execute post-deploy scripts
        $postDeployResult = $this->lifecycleScriptService->executeScripts($projectConfig['lifecycle']['post_deploy'], $deployVariables, $directory);
        $responseData['post_deploy'] = $postDeployResult;

        return ResponseHttp::generateJson((object)$responseData, 200);
    }
}