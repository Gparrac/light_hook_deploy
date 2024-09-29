# CLI Reference

This document provides an overview of the `CLI` scripts found in the cli directory. Each script serves a specific function in managing access, credentials, and deployment configurations for your projects. Below is the list of scripts and their usage options.

## lhd-install.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-install.sh)**

Installs the project and sets up which user has permission to execute the deployment scripts.

#### Options:

- `Username`: Specify the user who will have access to the deployment scripts.

## lhd-add-access-key.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-add-access-key.sh)**

Adds access credentials for a project, enabling it to be deployed via the API endpoint.

#### Options:

- `Project Name`: Name of the project for which access is being added.
- `Password`: Password used to authenticate the deployment via the API.

## lhd-update-access-key.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-update-access-key.sh)**

Updates the password of the access credentials for a specified project.

#### Options:

- `Project Name`: Name of the project for which the access credentials are updated.
- `Password`: New password to update the project credentials.

## lhd-delete-access-key.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-delete-access-key.sh)**

Deletes the access credentials for a specified project, removing its API access.

#### Options:

- `Project Name`: Name of the project for which access is being removed.

## lhd-list-access-key.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-list-access-key.sh)**

Lists all the access credentials for projects that are available via the API.

## lhd-add-secret-token.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-add-secret-token.sh)**

Creates credentials for a project, enabling it to use the webhook functionality of the remote repositories.

Options:

- `Project Name`: Name of the project for which the token is being created.
- `Token`: The secret token used to authenticate webhooks.

## lhd-update-secret-token.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-update-secret-token.sh)**

Updates the secret token of the project for webhook authentication.

Options:

- `Project Name`: Name of the project.
- `Token`: The new secret token for webhook authentication.

## lhd-delete-secret-token.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-delete-secret-token.sh)**

Deletes the webhook credentials for a specific project.

Options:

- `Project Name`: Name of the project for which the webhook credentials are removed.

## lhd-list-secret-token.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-list-secret-token.sh)**

Lists the webhook credentials for all projects, showing the project name and token in plain text.
## lhd-add-deployment-structure.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-add-deployment-structure.sh)**

Creates a deployment structure based on the credentials of an existing project, either via endpoint or webhook. The directory path specifies where deployment scripts will be executed.

Options:

- `Project Name`: Name of the project.
- `Directory Path`: The directory where the deployment will take place.

## lhd-update-deployment-directory.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-update-deployment-directory.sh)**

Updates the directory path where the deployment scripts for a project will be executed.

Options:

- `Project Name`: Name of the project.
- `Directory Path`: New directory path for the deployment.

## lhd-delete-deployment-structure.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-delete-deployment-structure.sh)**

Deletes the deployment structure for a specified project, including the lifecycle and script execution path.

Options:

- `Project Name`: Name of the project for which the deployment structure will be deleted.

## lhd-list-deployment-structure.sh

**[Source](https://github.com/Gparrac/light_hook_deploy/blob/main/cli/lhd-list-deployment-structure.sh)**

Lists all created deployment structures and their respective directories.