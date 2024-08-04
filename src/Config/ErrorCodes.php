<?php

namespace PipeLhd\Config;

class ErrorCodes
{
    // Errors related to request limit
    const TOO_MANY_REQUESTS = 'TOO_MANY_REQUESTS';

    // Errors related to Authentication 
    const MISSING_PARAMETERS = 'MISSING_PARAMETERS';
    const AUTHENTICATION_FAILED = 'AUTHENTICATION_FAILED';
    const PROJECT_ACCESS_NOT_FOUND = 'PROJECT_ACCESS_NOT_FOUND';

    // Errors related to Project 
    const PROJECT_NOT_REGISTERED = 'PROJECT_NOT_REGISTERED';
    const CONFIG_FILE_NOT_FOUND = 'CONFIG_FILE_NOT_FOUND';
    const INVALID_PROJECT_CONFIG = 'INVALID_PROJECT_CONFIG';

    // Errors releated to Scripts
    public const MISSING_SCRIPTS = 'MISSING_SCRIPTS'; 

    // Errors releated to Directory
    const DIRECTORY_NOT_SPECIFIED = 'DIRECTORY_NOT_SPECIFIED';
    const DIRECTORY_NOT_ACCESSIBLE = 'DIRECTORY_NOT_ACCESSIBLE';
    
    // Generic Codes
    const UNEXPECTED_ERROR = 'UNEXPECTED_ERROR';
    const SCRIPT_EXECUTION_FAILED = 'SCRIPT_EXECUTION_FAILED';
}