<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class InvalidCredentialsException extends DeploymentException
{
    protected $code = ErrorCodes::AUTHENTICATION_FAILED;
}