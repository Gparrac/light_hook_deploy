<?php

namespace PipeLhd\Middlewares\Specific\WebhookTokenStrategy;

use Psr\Http\Message\ServerRequestInterface as Request;

class WebhookTokenStrategyFactory
{
    public static function createStrategy(Request $request): WebhookTokenStrategy
    {
        $userAgent = $request->getHeaderLine('User-Agent');
        
        if (strpos($userAgent, 'GitHub-Hookshot') !== false) {
            return new GitHubTokenStrategy();
        } elseif (strpos($userAgent, 'GitLab') !== false) {
            return new GitLabTokenStrategy();
        } elseif (strpos($userAgent, 'Bitbucket') !== false) {
            return new BitbucketTokenStrategy();
        } else {
            throw new \Exception('Unsupported webhook service');
        }
    }
}