# Configuring Deployment Scripts for Light Hook Deploy

To set up the deployment structure for your project, you need to use the following CLI script:

## Command to Create Deployment Structure

```bash
./lhd-add-deployment-structure.sh <project_name> <directory_path>
```

- `<project_name>`: This should be the same project name used in other related commands for access keys or secret tokens:
    - For access keys ([deployment via endpoint](./webhook-deployment-via-endpoint.md)):
    ```bash
    ./lhd-add-access-key.sh <project_name> <password>
    ```
    - For secret tokens (used for [webhooks](./webhook-based-deployments.md)):
    ```bash
    ./lhd-add-secret-token.sh <project_name> <token>
    ```

## Listing Existing Projects

In case you forget the project names, you can list them using the following commands:

- To list the projects configured for endpoint access:

```bash
./lhd-list-access-key.sh
```

- To list the projects configured with secret tokens for webhooks:

```bash
./lhd-list-secret-token.sh
```


## Defining the Project Path

When creating the deployment structure, you must provide the directory path where the project is located:

```bash
./lhd-add-deployment-structure.sh <project_name> <directory_path>
```

- `<directory_path>`: This is the directory where all deployment scripts will be executed.


## Permissions and User Access

If the project directory is located in another user’s directory, for example, `/home/user_project_directory/`, you must ensure that the user executing the deployment scripts has access to that directory. This can be done by adding the deployment user to the group of the other user. The deployment user will only get access to the necessary files and scripts without receiving system-wide permissions.

You can add the user executing the deployment scripts to the other user's group using the following command:

```bash
sudo usermod -a -G <user_with_permissions> <user_deploy>
```

- `<user_with_permissions>`: The user who owns the directory where the project is located (e.g., `/home/user_project_directory/`).
- `<user_deploy>`: The user who is responsible for executing the deployment scripts.

Additionally, it's essential that the directory `/home/user_project_directory/` has the correct ownership permissions. The directory should be owned by the specific user and group rather than root. You can set this with the following command:

```bash
sudo chown <user_project_directory>:<user_project_directory> /home/user_project_directory/
```

The format `user_project_directory:user_project_directory` refers to the username for both the owner and the group of the directory. This ensures that the directory is accessible by the intended user, allowing the deployment user to access the necessary project files for script execution without broader system access.

### User Permission Validation

It’s essential to validate that the user responsible for executing the deployment scripts has the necessary permissions to access the project directory and run the scripts. Ensure the deployment user can navigate into the folder and has the correct permissions for successful deployment execution.


### 🛠️ Deployment Structure Creation

Once you run the deployment structure script:

```bash
./lhd-add-deployment-structure.sh <project_name> <directory_path>
```

This command creates two main directories under the `deployments` folder:

- 📁 `projects/`: This contains the actual deployment configuration files for each project.
- 🔗 `symlinks/`: This directory holds symbolic links that point to the actual deployment directories of your projects. If you’re managing multiple projects on the same server, all the deployment paths will appear here, giving you quick access to the scripts for execution.

### 🔍 Folder Breakdown

`projects/`: Each project has a `.php` configuration file named after the project. When you run the `lhd-add-deployment-structure.sh` script, it generates this structure:

```php
<?php
// Deployment configuration for project 'project_name'
return [
    'directory' => 'symlinks/project_name', // -> /path/directory/project_name/
    'lifecycle' => [
        'pre_deploy' => [
            // 'pre_deploy_script1.sh',
            // 'pre_deploy_script2.sh',
        ],
        'deploy' => [
            // 'deploy_script1.sh',
            // 'deploy_script2.sh',
        ],
        'post_deploy' => [
            // 'post_deploy_script1.sh',
            // 'post_deploy_script2.sh',
        ],
        'rollback' => [
            // 'rollback_script1.sh',
            // 'rollback_script2.sh',
        ],
    ],
    'deploy_variables' => [
        // 'DB_NAME' => 'dev_db',
        // 'DB_USER' => 'dev_user',
        // 'DB_PASSWORD' => 'dev_password',
    ],
];
```

In this configuration:

- The directory field is the symbolic link for your project pointing to its actual directory (commented for reference).
- The lifecycle array defines the scripts executed at different stages of the deployment process.
- The deploy_variables section lets you store reusable variables for your scripts, such as database credentials or environment-specific settings.

### 🚀 Deployment Lifecycle Overview

The system follows a structured lifecycle for deployment, consisting of four stages:

1. 🛡️ **Pre-deploy** (`pre_deploy`): Scripts executed before starting the deployment. Ideal for tasks like backing up files or storing a commit hash.
2. ⚙️ **Deploy** (`deploy`): The actual deployment scripts, responsible for actions like running migrations, pulling updates (git pull), downloading Docker images, or any other core deployment tasks.
3. 🔄 **Rollback** (`rollback`): These scripts are executed if any stage fails. If any script in pre_deploy, deploy, or post_deploy fails, rollback scripts will be triggered to undo the changes.
4. 📦 **Post-deploy** (`post_deploy`): Scripts for clean-up actions after deployment, such as deleting temporary files, restarting services, or clearing caches.

#### 📋 Endpoint Explanation

There are three main endpoints for handling the deployment lifecycle:

- **`/lhd/deploy`**: Executes the scripts in `pre_deploy`, `deploy`, and `post_deploy` stages. This endpoint is used for **manual deployments**.

- **`/lhd/rollback`**: Executes the `rollback` scripts followed by `post_deploy` scripts. This endpoint is also for **manual deployments**.

- **`/lhd/all-in-one` or `/lhd/git/all-in-one`**: Combines both `/deploy` and `/rollback` execution in case of failures. *Note: `/lhd/git/all-in-one` is the only endpoint specifically designed for webhook-based deployments, while the other two are for manual endpoint deployments.*

Deployment Flow:

- **Pre-deploy:** Executes backup and contingency scripts before starting deployment.
- **Deploy:** Runs core deployment tasks such as code updates or migrations.
- **Rollback:** Activated in case any of the above fails. It executes rollback scripts to revert the changes.
- **Post-deploy:** Cleans up unnecessary files and restarts services as needed.

### 📝 Script Variables

Deployment scripts often require environment-specific variables. You can define these variables under the `deploy_variables` section, like this:

```php
'deploy_variables' => [
    'DB_NAME' => 'dev_db',
    // 'DB_USER' => 'dev_user',
    // 'DB_PASSWORD' => 'dev_password',
]
```
In your `.sh` scripts, use these variables like so:

```bash
echo "Database: {{DB_NAME}}"
```

The system will replace `{{DB_NAME}}` with the actual value (`dev_db` in this case) when executing the script.

## 📂 Scripts Directory

Within the `deployments` folder, there’s another important directory called `scripts/`.

This directory serves as the **central storage** for all the scripts executed during the deployment lifecycle. Here’s how it fits into the overall structure:

- **Location:** `/deployments/scripts/`
- **Purpose:** It stores all the scripts for the **pre_deploy**, **deploy**, **post_deploy**, and **rollback** phases, used by each project in the `projects/` folder.

All the scripts required for a project’s deployment will be executed from this folder. It’s crucial to understand that every script involved in the lifecycle of a deployment is stored here.


### Note: 🔑 Sudo Permissions for Scripts

Remember, When you initially install the system by running:

```bash
./lhd-install.sh
```

It automatically configures **sudo execution permissions** for all the scripts within the `/deployments/scripts/` directory. This means:

- Every script in this directory can be executed with `sudo` privileges.
- This enables scripts to perform critical operations like:
    - Running a `git pull` to fetch the latest changes.
    - Restarting services (e.g., Apache, Nginx, Docker containers).
    - Performing file system operations that require elevated permissions.

This setup ensures that your deployment lifecycle scripts have the necessary permissions to handle tasks that require system-level access, without manually entering `sudo` every time.


## 📜 Script Format for Deployment

To ensure that your deployment scripts are correctly recognized by the Light Hook Deploy system, it is essential to follow a specific format for error handling. When an error occurs during the execution of a script, the system must receive a specific output to detect the failure.

### Error Detection

To signal an error, your script must include an `echo` statement that outputs either `error_lhd` or `Error_lhd` (case insensitive). The presence of this string in the output indicates to the system that there has been a failure in the execution of the script.

#### Example Scripts

##### Git Pull Script 
Here's an example of a basic script that performs a `git pull` operation (`git-pull.sh`):

```bash
#!/bin/bash

output=$(sudo git pull origin {{BRANCH}} 2>&1)
status=$?
if [ $status -ne 0 ]; then
    echo "Error_lhd: $output"
    exit 1
else
    echo "Success: downloaded repository changes"
    exit 0
fi
```


##### Explanation of the Script

- The script starts with the shebang (`#!/bin/bash`), indicating that it should be executed using the Bash shell.
- It attempts to pull the latest changes from the specified branch using `git pull origin {{BRANCH}}`. The output and any errors are captured in the variable `output`.
- The script checks the exit status of the `git pull` command. If the status is not zero (indicating an error), it outputs `Error_lhd` along with the error message and exits with a status of `1`. This informs the system of the failure.
- If the command is successful, it echoes a success message and exits with a status of `0`.

#### Pre-deployment Script to Save Current Commit
It is advisable to create separate scripts for better tracking. For instance, you can save the current commit hash before performing a git pull, which can be helpful for rollback purposes. Here's a pre-deployment script for this:

```bash
#!/bin/bash

# Get the current commit hash
current_commit_hash=$(git rev-parse HEAD)
status=$?
if [ $status -ne 0 ]; then
    echo "error_lhd: Failed to get the current commit hash: $current_commit_hash"
    exit 1
fi

# Create a temporary file to save the commit hash
temp_file="commit_hash_{{NAME_COMMIT_HASH}}.txt"
touch "$temp_file"
status=$?
if [ $status -ne 0 ]; then
    echo "error_lhd: Failed to create the temporary file: $temp_file"
    exit 1
fi

# Write the commit hash to the temporary file
echo "$current_commit_hash" > "$temp_file"
status=$?
if [ $status -ne 0 ]; then
    echo "error_lhd: Failed to write to the temporary file: $temp_file"
    exit 1
fi

echo "success: The current commit hash has been saved in the temporary file: $temp_file"
exit 0
```

#### Rollback Script

This script helps to roll back the repository to the previously saved commit hash in case of a failure:

```bash
#!/bin/bash

# Temporary file that contains the commit hash
temp_file="commit_hash_{{NAME_COMMIT_HASH}}.txt"

# Check if the temporary file exists
if [ ! -f "$temp_file" ]; then
    echo "error_lhd: Temporary file $temp_file does not exist"
    exit 1
fi

# Read the commit hash from the temporary file
commit_hash=$(cat "$temp_file")
status=$?
if [ $status -ne 0 ]; then
    echo "error_lhd: Failed to read the commit hash from $temp_file"
    exit 1
fi

# Reset to the specified commit
git reset --hard "$commit_hash"
status=$?
if [ $status -ne 0 ]; then
    echo "error_lhd: Failed to reset to the commit $commit_hash"
    exit 1
fi

echo "success: The repository has been reset to the commit $commit_hash"
exit 0
```

#### Note on Variables
- The `{{BRANCH}}` variable in the git pull script should be defined in your configuration settings and should correspond to the branch you wish to pull changes from.
- The `{{NAME_COMMIT_HASH}}` placeholder in the temporary file names should be replaced with a meaningful identifier to distinguish between different deployment instances.