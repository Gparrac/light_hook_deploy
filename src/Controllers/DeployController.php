<?php

namespace PipeLhd\Controllers;

use PipeLhd\Services\LifecycleScriptService;
use PipeLhd\Services\ScriptExecutor\ScriptWithVariables;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PipeLhd\Utils\ResponseHttp;
use PipeLhd\Exceptions\ScriptExecutionFailedException;

class DeployController
{
    private $lifecycleScriptService;

    public function __construct()
    {
        $this->lifecycleScriptService = new LifecycleScriptService(new ScriptWithVariables);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $projectConfig = $request->getAttribute('project_config');
            $deployVariables = $projectConfig['deploy_variables'] ?: [];
            $directory = DEPLOYMENT_PATH . $projectConfig['directory'];
    
            // Execute pre-deploy scripts
            $preDeployResult = $this->lifecycleScriptService->executeScripts($projectConfig['lifecycle']['pre_deploy'], $deployVariables, $directory);
            $responseData = [
                'pre_deploy' => $preDeployResult
            ];
    
            if ($preDeployResult['status'] === 'error') {
                throw new ScriptExecutionFailedException("Pre-deploy script execution failed");
            }
    
            // Execute deploy scripts
            $deployResult = $this->lifecycleScriptService->executeScripts($projectConfig['lifecycle']['deploy'], $deployVariables, $directory);
            $responseData['deploy'] = $deployResult;
    
            if ($deployResult['status'] === 'error') {
                throw new ScriptExecutionFailedException("Deploy script execution failed");
            }
    
            // Execute post-deploy scripts
            $postDeployResult = $this->lifecycleScriptService->executeScripts($projectConfig['lifecycle']['post_deploy'], $deployVariables, $directory);
            $responseData['post_deploy'] = $postDeployResult;
            
            if ($postDeployResult['status'] === 'error') {
                throw new ScriptExecutionFailedException("Post-deploy script execution failed");
            }
    
            return ResponseHttp::generateJson($responseData, 200);
        } catch (ScriptExecutionFailedException $e) {
            $responseData['error'] = $e->getMessage();
            return ResponseHttp::generateJson(
                $responseData,
                500,
                false,
                $e->getCode()
            );
        }
    }
}