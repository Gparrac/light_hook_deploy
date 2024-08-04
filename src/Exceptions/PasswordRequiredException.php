<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class PasswordRequiredException extends DeploymentException
{
    protected $code = ErrorCodes::MISSING_PARAMETERS;
}