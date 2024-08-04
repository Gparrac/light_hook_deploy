<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class ScriptExecutionFailedException extends DeploymentException
{
    protected $code = ErrorCodes::SCRIPT_EXECUTION_FAILED;
}