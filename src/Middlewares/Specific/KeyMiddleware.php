<?php

namespace PipeLhd\Middlewares\Specific;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use PipeLhd\Utils\ResponseHttp;
use PipeLhd\Exceptions as CustomException;

class KeyMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        try {
            if (strpos($request->getHeaderLine('Content-Type'), 'application/json') !== false) {
                $params = json_decode($request->getBody()->getContents(), true);
            } else {
                $params = $request->getParsedBody();
            }

            $projects = include ROOT_PATH . '/src/Config/keys.php';

            if (!is_array($projects) || !isset($params['project']) || !array_key_exists($params['project'], $projects)) {
                throw new CustomException\ProjectAccessNotFoundException("Project access not found");
            }

            if (!isset($params['password'])) {
                throw new CustomException\PasswordRequiredException("Password is required");
            }

            $storedHash = $projects[$params['project']];
            if (!password_verify($params['password'], $storedHash)) {
                throw new CustomException\InvalidCredentialsException("Invalid credentials");
            }

            $request = $request->withAttribute('project', $params['project']);
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