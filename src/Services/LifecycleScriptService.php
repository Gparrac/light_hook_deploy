<?php

namespace PipeLhd\Services;

use PipeLhd\Services\ScriptExecutor\ScriptExecutorInterface;

class LifecycleScriptService
{
    private $scriptExecutor;

    public function __construct(ScriptExecutorInterface $scriptExecutor)
    {
        $this->scriptExecutor = $scriptExecutor;
    }

    public function executeScripts(array $scripts, array $deployVariables, string $directory): array
    {
        $results = [];
        foreach ($scripts as $script) {
            $result = $this->scriptExecutor->executeScript($script, $deployVariables, $directory);
            $results[] = [
                'status' => $result['status'],
                'script' => $script,
                'message' => $result['message']
            ];

            if ($result['status'] === 'error') {
                return [
                    'status' => 'error',
                    'details' => $results
                ];
            }
        }

        return [
            'status' => 'success',
            'details' => $results
        ];
    }
}