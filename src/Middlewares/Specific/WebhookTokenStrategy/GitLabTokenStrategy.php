<?php

namespace PipeLhd\Middlewares\Specific\WebhookTokenStrategy;

use Psr\Http\Message\ServerRequestInterface as Request;

class GitLabTokenStrategy implements WebhookTokenStrategy
{
    public function validateToken(Request $request, string $secretToken): bool
    {
        return $request->getHeaderLine('X-Gitlab-Token') == $secretToken;
    }
}