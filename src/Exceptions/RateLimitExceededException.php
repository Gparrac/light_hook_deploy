<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class RateLimitExceededException extends DeploymentException
{
    protected $code = ErrorCodes::TOO_MANY_REQUESTS;
}