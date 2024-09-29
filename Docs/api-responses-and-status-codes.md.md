# API Responses and Status Codes

This document outlines the various responses returned by the Light Hook Deploy API, including their status codes and the structure of the response payloads.

## Example Deployment Configuration

The following is an example configuration file (`project_example.php`) for a project named 'project'. This file outlines the directory and the lifecycle scripts involved in the deployment process:

```php
<?php
// Deployment configuration for project 'project'
return [
    'directory' => 'symlinks/project', // ->  /var/www/html/project/
    'lifecycle' => [
        'pre_deploy' => [
            'save-commit-hash.sh'
        ],
        'deploy' => [
            'git-pull.sh'
        ],
        'post_deploy' => [
            'remove-commit-hash.sh'
        ],
        'rollback' => [
            'rollback.sh',
        ],
    ],
    'deploy_variables' => [
        'BRANCH' => 'main',
        'NAME_COMMIT_HASH' => "aad13lo04kfcd",
    ],
];
```

## Example Response for `/lhd/all-in-one` and `/lhd/git/all-in-one`

### 200 OK

A successful deployment response with a status of `200`. This indicates that the deployment process was executed without any issues, and the rollback was not needed. Below is an example of the response structure:

```json
{
    "status": "success",
    "details": {
        "deploy": {
            "status": "success",
            "details": {
                "pre_deploy": {
                    "status": "success",
                    "details": [
                        {
                            "status": "success",
                            "script": "save-commit-hash.sh",
                            "message": "Executed: Successfully saved the current commit hash in the temporary file: commit_hash_aad13lo04kfcd.txt\n"
                        }
                    ]
                },
                "deploy": {
                    "status": "success",
                    "details": [
                        {
                            "status": "success",
                            "script": "git-pull.sh",
                            "message": "Executed: Deployment script executed successfully. All changes have been applied.\n"
                        }
                    ]
                },
                "post_deploy": {
                    "status": "success",
                    "details": [
                        {
                            "status": "success",
                            "script": "remove-commit-hash.sh",
                            "message": "Executed: The temporary file commit_hash_aad13lo04kfcd.txt has been deleted successfully.\n"
                        }
                    ]
                }
            }
        },
        "rollback": "Rollback not activated"
    }
}
```

#### Explanation of Fields

- **status**: Indicates the overall status of the deployment process (e.g., `success`, `error`).
- **details**: Contains detailed information about each stage of the deployment (e.g., `pre_deploy`, `deploy`, `post_deploy`).
- **script**: The name of the script that was executed during the deployment process.
- **message**: A message detailing the result of the script execution.

This response structure provides clarity on what occurred during the deployment process and allows users to understand the results of each script executed.

### 202 Accepted

A partially successful deployment response with a status of `202`. This indicates that while the deployment process was initiated, an error occurred during the deployment stage, triggering a rollback. Below is an example of the response structure:

```json
{
    "status": "success",
    "details": {
        "deploy": {
            "status": "error",
            "details": {
                "pre_deploy": {
                    "status": "success",
                    "details": [
                        {
                            "status": "success",
                            "script": "save-commit-hash.sh",
                            "message": "Executed: The current commit hash has been successfully recorded in the temporary file: commit_hash_aad13lo04kfcd.txt\n"
                        }
                    ]
                },
                "deploy": {
                    "status": "error",
                    "details": [
                        {
                            "status": "error",
                            "script": "git-pull.sh",
                            "message": "Script execution failed: Error_lhd: fatal: The current branch main has no upstream branch.\n"
                        }
                    ]
                },
                "error": "Deploy script execution failed"
            },
            "error_code": "SCRIPT_EXECUTION_FAILED"
        },
        "rollback": {
            "status": "success",
            "details": {
                "rollback": {
                    "status": "success",
                    "details": [
                        {
                            "status": "success",
                            "script": "rollback.sh",
                            "message": "Executed: The rollback process completed successfully. Previous state restored.\n"
                        }
                    ]
                },
                "post_deploy": {
                    "status": "success",
                    "details": [
                        {
                            "status": "success",
                            "script": "remove-commit-hash.sh",
                            "message": "Executed: The temporary file commit_hash_aad13lo04kfcd.txt has been removed successfully after rollback.\n"
                        }
                    ]
                }
            }
        }
    }
}
```
#### Explanation of Fields

- status: Indicates the overall status of the deployment process (e.g., `success`, `error`).
- **details**: Contains detailed information about each stage of the deployment (e.g., `pre_deploy`, `deploy`, `rollback`).
- **script**: The name of the script that was executed during the deployment process.
- **message**: A message detailing the result of the script execution, including any errors encountered during the process.
- **error**: Indicates the reason for the failure in the deployment process.
- **error_code**: A code representing the type of error encountered.

This response structure provides clarity on the outcomes of the deployment process, illustrating that while the pre-deployment was successful, an error occurred during the deployment, prompting a successful rollback to maintain system integrity.

### 500 Internal Server Error

A response indicating a critical failure during the deployment process with a status of `500`. This indicates that both the deployment and the rollback processes encountered errors. Below is an example of the response structure:

```json
{
    "status": "error",
    "details": {
        "deploy": {
            "status": "error",
            "details": {
                "pre_deploy": {
                    "status": "success",
                    "details": [
                        {
                            "status": "success",
                            "script": "save-commit-hash.sh",
                            "message": "Executed: The current commit hash has been successfully stored in the temporary file: commit_hash_aad13lo04kfcd.txt\n"
                        }
                    ]
                },
                "deploy": {
                    "status": "error",
                    "details": [
                        {
                            "status": "error",
                            "script": "git-pull.sh",
                            "message": "Script execution failed: Error_lhd: fatal: The current branch main has no upstream branch.\n"
                        }
                    ]
                },
                "error": "Deploy script execution failed"
            },
            "error_code": "SCRIPT_EXECUTION_FAILED"
        },
        "rollback": {
            "status": "error",
            "details": {
                "rollback": {
                    "status": "error",
                    "details": [
                        {
                            "status": "error",
                            "script": "rollback.sh",
                            "message": "Script execution failed: Error_lhd: fatal: could not read commit hash\n"
                        }
                    ]
                },
                "error": "Rollback script execution failed"
            },
            "error_code": "SCRIPT_EXECUTION_FAILED"
        }
    },
    "error_code": "SCRIPT_EXECUTION_FAILED"
}
```

#### Explanation of Fields

- **status**: Indicates the overall status of the deployment process (e.g., `error`).
- **details**: Contains detailed information about each stage of the deployment and rollback processes.
- **script**: The name of the script that was executed during the deployment or rollback process.
- **message**: A message detailing the result of the script execution, including any errors encountered.
- **error**: Indicates the reason for the failure in the deployment or rollback process.
- **error_code**: A code representing the type of error encountered, applicable to both deployment and rollback.

This response structure illustrates that both the deployment and rollback processes encountered critical failures, highlighting the importance of proper error handling during deployment operations. The detailed messages provide context for troubleshooting the issues encountered.

## Error Codes Overview
This table presents various error codes, their descriptions, and the corresponding status numbers that may occur during deployment processes.

| **Error Code**                    | **Description**                                                                                                                                | **Status Number**    | **Possible Solutions**                                                                                      |
|-----------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|----------------------|-------------------------------------------------------------------------------------------------------------|
| `TOO_MANY_REQUESTS`               | Occurs when Redis is active and the number of deployment attempts by IP exceeds the allowed limit within a specified time frame.            | 429                  | Wait for the cooldown period before retrying.                                                              |
| `MISSING_PARAMETERS`               | Occurs when required parameters are not provided in the request body, such as `{"project": "project", "password": "password"}`.             | 400                  | Ensure the request includes all required parameters.                                                        |
| `AUTHENTICATION_FAILED`            | Indicates that the provided credentials are invalid; the project and password do not match.                                                  | 400                  | Verify and correct the project name and password.                                                          |
| `PROJECT_ACCESS_NOT_FOUND`        | Indicates that the project does not exist in `./deployments/projects/`.                                                                        | 400                  | Use CLI: `./lhd-add-deployment-structure <project> <path-directory>` to add the necessary project structure. |
| `PROJECT_NOT_REGISTERED`          | Indicates that the project does not have configured credentials.                                                                               | 400                  | Run `./lhd-add-secret-token.sh` for webhooks or `./lhd-add-access-key.sh` for endpoint usage to register credentials. |
| `CONFIG_FILE_NOT_FOUND`           | Occurs when `./deployments/deployments.php` does not exist, which holds the path for deployment project structures.                          | 400                  | Create the file using `./lhd-add-deployment-structure <project> <path-directory>`.                         |
| `INVALID_PROJECT_CONFIG`          | Occurs when the deployment configuration file is not formatted correctly or is not an array.                                                 | 400                  | Review the configuration file to ensure it is correctly formatted as an array.                             |
| `MISSING_SCRIPTS`                 | Indicates that one of the scripts defined in the lifecycle is missing.                                                                        | 400                  | Check for missing scripts and add them to the specified paths.                                            |
| `DIRECTORY_NOT_SPECIFIED`         | Indicates that no directory was specified in the deployment configuration.                                                                    | 400                  | Ensure to specify the directory in the deployment configuration file.                                      |
| `DIRECTORY_NOT_ACCESSIBLE`        | Occurs when the user running the program does not have access to the specified folder, typically due to permission issues.                   | 400                  | Adjust folder permissions or execute the program as a user with access set using `./lhd-install.sh <username>`. |
| `UNEXPECTED_ERROR`                | Indicates a general error in the system.                                                                                                      | 500                  | Investigate logs for specific error messages to determine the underlying issue.                            |
| `SCRIPT_EXECUTION_FAILED`         | Occurs when there is an error executing one of the scripts.                                                                                   | 500                  | Review the script for errors, ensure it exists, and check permissions.                                     |

