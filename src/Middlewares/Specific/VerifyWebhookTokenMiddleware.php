<?php

namespace PipeLhd\Middlewares\Specific;

use PipeLhd\Middlewares\Specific\WebhookTokenStrategy\WebhookTokenStrategyFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use PipeLhd\Utils\ResponseHttp;
use PipeLhd\Exceptions as CustomException;

class VerifyWebhookTokenMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $projectName = $queryParams['project'] ?? null;

            $projects = include ROOT_PATH . '/src/Config/tokens.php';

            if (!is_array($projects) || $projectName === null || !array_key_exists($projectName, $projects)) {
                throw new CustomException\ProjectAccessNotFoundException("Project access not found");
            }

            $webhookStrategy = WebhookTokenStrategyFactory::createStrategy($request);

            if (!$webhookStrategy->validateToken($request, $projects[$projectName])) {
                throw new CustomException\InvalidCredentialsException("Invalid secret token");
            }

            $request = $request->withAttribute('project', $projectName);
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