<?php

namespace PipeLhd\Exceptions;

use PipeLhd\Config\ErrorCodes;

class DirectoryNotSpecifiedException extends DeploymentException
{
    protected $code = ErrorCodes::DIRECTORY_NOT_SPECIFIED;
}