<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class ConfigFileNotFoundException extends DeploymentException
{
    protected $code = ErrorCodes::CONFIG_FILE_NOT_FOUND;
}