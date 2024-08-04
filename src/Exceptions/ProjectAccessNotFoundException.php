<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class ProjectAccessNotFoundException extends DeploymentException
{
    protected $code = ErrorCodes::PROJECT_ACCESS_NOT_FOUND;
}