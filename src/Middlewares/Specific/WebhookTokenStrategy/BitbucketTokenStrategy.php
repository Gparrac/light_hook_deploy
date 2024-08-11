<?php

namespace PipeLhd\Middlewares\Specific\WebhookTokenStrategy;

use Psr\Http\Message\ServerRequestInterface as Request;

class BitbucketTokenStrategy implements WebhookTokenStrategy
{
    public function validateToken(Request $request, string $secretToken): bool
    {
        $payload = $request->getBody()->getContents();

        $signature = $request->getHeaderLine('X-Hub-Signature');

        $calculatedHash = hash_hmac('sha256', $payload, $secretToken);

        return hash_equals($signature, 'sha256=' . $calculatedHash);
    }
}