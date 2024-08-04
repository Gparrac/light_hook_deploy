<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class DirectoryNotAccessibleException extends DeploymentException
{
    protected $code = ErrorCodes::DIRECTORY_NOT_ACCESSIBLE;
}