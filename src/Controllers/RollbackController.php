<?php

namespace PipeLhd\Controllers;

use PipeLhd\Services\ScriptExecutor\ScriptWithVariables;
use PipeLhd\Services\LifecycleScriptService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PipeLhd\Utils\ResponseHttp;
use PipeLhd\Exceptions\ScriptExecutionFailedException;

class RollbackController
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
            $directory = $projectConfig['directory'];
            $directory = ROOT_PATH . '/deployments/' . $projectConfig['directory'];

            // Execute rollback scripts
            $rollbackResult = $this->lifecycleScriptService->executeScripts($projectConfig['lifecycle']['rollback'], $deployVariables, $directory);
            $responseData = [
                'rollback' => $rollbackResult
            ];

            if ($rollbackResult['status'] === 'error') {
                throw new ScriptExecutionFailedException("Rollback script execution failed");
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