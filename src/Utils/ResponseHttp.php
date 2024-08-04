<?php

namespace PipeLhd\Utils;

use Nyholm\Psr7\Response;

class ResponseHttp
{
    public static function generateJson(
        array $messageJson = [],
        int $statusCode = 200,
        bool $statusSuccess = true,
        string $errorCode = ''
    ): Response
    {
        $responseArray = [
            'status' => $statusSuccess ? 'success' : 'error',
            'details' => $messageJson
        ];

        if (!$statusSuccess) {
            $responseArray['error_code'] = $errorCode;
        }

        $response = new Response();
        $response->getBody()->write(json_encode($responseArray));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}