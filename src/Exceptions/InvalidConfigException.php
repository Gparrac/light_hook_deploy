<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class InvalidConfigException extends DeploymentException
{
    protected $code = ErrorCodes::INVALID_PROJECT_CONFIG;
}