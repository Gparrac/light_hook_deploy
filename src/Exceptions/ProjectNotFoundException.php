<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class ProjectNotFoundException extends DeploymentException
{
    protected $code = ErrorCodes::PROJECT_NOT_REGISTERED;
}