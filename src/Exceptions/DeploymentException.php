<?php

namespace PipeLhd\Exceptions;

use Exception;

class DeploymentException extends Exception
{
    protected string $errorCode;
    
    public function __construct(string $message, string $errorCode = '', Exception $previous = null)
    {
        $this->errorCode = $errorCode;
        parent::__construct($message, 0, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}