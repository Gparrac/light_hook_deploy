<?php

namespace PipeLhd\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PipeLhd\Utils\ResponseHttp;
use PipeLhd\Config\ErrorCodes;

class AllInOneController
{
    private $deployController;
    private $rollbackController;

    public function __construct(DeployController $deployController, RollbackController $rollbackController)
    {
        $this->deployController = $deployController;
        $this->rollbackController = $rollbackController;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        // Execute deploy
        $deployResult = $this->deployController->__invoke($request, $response, $args);
        $deployStatusCode = $deployResult->getStatusCode();
        $deployBody = json_decode((string)$deployResult->getBody(), true);

        if ($deployStatusCode !== 200) {
            // Execute rollback if deploy fails
            $rollbackResult = $this->rollbackController->__invoke($request, $response, $args);
            $rollbackStatusCode = $rollbackResult->getStatusCode();
            $rollbackBody = json_decode((string)$rollbackResult->getBody(), true);

            $statusCode = $rollbackStatusCode === 200 ? 202 : 500;
            $responseBody = [
                'deploy' => $deployBody,
                'rollback' => $rollbackBody
            ];

            return ResponseHttp::generateJson($responseBody, $statusCode, $rollbackStatusCode !== 500, $rollbackStatusCode === 500 ? ErrorCodes::SCRIPT_EXECUTION_FAILED : '');
        } else {
            $statusCode = 200;
            $responseBody = [
                'deploy' => $deployBody,
                'rollback' => 'Rollback not activated'
            ];

            return ResponseHttp::generateJson($responseBody, $statusCode, true);
        }
    }
}