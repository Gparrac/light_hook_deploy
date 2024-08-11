<?php

namespace PipeLhd\Middlewares\Specific\WebhookTokenStrategy;

use Psr\Http\Message\ServerRequestInterface as Request;

interface WebhookTokenStrategy
{
    public function validateToken(Request $request, string $secretToken): bool;
}