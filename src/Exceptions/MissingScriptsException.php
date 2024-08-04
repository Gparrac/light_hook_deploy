<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class MissingScriptsException extends DeploymentException
{
    protected $code = ErrorCodes::MISSING_SCRIPTS;
}