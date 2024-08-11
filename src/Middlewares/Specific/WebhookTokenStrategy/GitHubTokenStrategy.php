<?php

namespace PipeLhd\Middlewares\Specific\WebhookTokenStrategy;

use Psr\Http\Message\ServerRequestInterface as Request;

class GitHubTokenStrategy implements WebhookTokenStrategy
{
    public function validateToken(Request $request, string $secretToken): bool
    {
        $payload = $request->getBody()->getContents();
        
        $signature = $request->getHeaderLine('X-Hub-Signature');

        $calculatedHash = hash_hmac('sha1', $payload, $secretToken);

        return hash_equals($signature, 'sha1=' . $calculatedHash);
    }
}