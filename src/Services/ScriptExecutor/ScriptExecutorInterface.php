<?php

namespace PipeLhd\Services\ScriptExecutor;

interface ScriptExecutorInterface
{
    public function executeScript(string $script, array $deployVariables = [], string $projectDir = ROOT_PATH): array;
}