#!/bin/bash

print_message() {
    COLOR=$1
    ICON=$2
    MESSAGE=$3
    echo -e "\n\e[${COLOR}m${ICON} ${MESSAGE}\e[0m\n"
}

# Ensure correct usage
if [ "$#" -lt 1 ]; then
    print_message "31;1" "❗" " Usage: $0 <project_name> [directory_path]"
    exit 1
fi

PROJECT_NAME=$1
DIRECTORY_PATH=$2
SCRIPT_DIR=$(dirname "$(realpath "$0")")

DEPLOYMENTS_DIR="$SCRIPT_DIR/../deployments"
PROJECTS_DIR="$DEPLOYMENTS_DIR/projects"
DEPLOYMENT_FILE="$DEPLOYMENTS_DIR/deployments.php"
KEYS_FILE="$SCRIPT_DIR/../src/Config/keys.php"

# Check if the project is listed in keys.php
if ! php -r "\$keys = require('$KEYS_FILE'); exit(array_key_exists('$PROJECT_NAME', \$keys) ? 0 : 1);" ; then
    print_message "31;1" "❗" " Error: Project '$PROJECT_NAME' is not listed in $KEYS_FILE."
    print_message "33;1" "ℹ️" "  Please create the access key for this project using 'lhd-add-access-key.sh'."
    exit 1
fi

mkdir -p "$PROJECTS_DIR"

# Create the PHP content for the project-specific deployment file
PROJECT_FILE="$PROJECTS_DIR/$PROJECT_NAME.php"
PROJECT_PHP_CODE=$(cat <<EOF
<?php
// Deployment configuration for project '$PROJECT_NAME'
return [
    'directory' => '${DIRECTORY_PATH:-/default/directory/}',
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
EOF
)

# Save the PHP code for the project-specific file
echo "$PROJECT_PHP_CODE" > "$PROJECT_FILE"
print_message "32;1" "✅" " Project-specific deployment file created: $PROJECT_FILE"

# Prepare PHP code for deployments.php
if [ -f "$DEPLOYMENT_FILE" ]; then
    # Include the existing deployments.php content and add new entry
    PHP_CODE=$(php -r "
        \$deployments = require('$DEPLOYMENT_FILE');
        \$deployments['$PROJECT_NAME'] = 'projects/$PROJECT_NAME.php';
        echo '<?php return ' . var_export(\$deployments, true) . ';';
    ")
else
    # Create a new deployments.php with the new entry
    PHP_CODE=$(cat <<EOF
<?php return array (
    '$PROJECT_NAME' => 'projects/$PROJECT_NAME.php',
);
EOF
    )
fi

# Save the updated deployments.php file
echo "$PHP_CODE" > "$DEPLOYMENT_FILE"
print_message "32;1" "✅" " Updated deployments file: $DEPLOYMENT_FILE"

# Check if directory path is provided
if [ -n "$DIRECTORY_PATH" ]; then
    # Check if the directory exists
    if [ -d "$DIRECTORY_PATH" ]; then
        # Check if script is run with sudo
        if [ "$(id -u)" -ne 0 ]; then
            print_message "31;1" "❗" " Error: Directory path provided. Please run the script with sudo."
            exit 1
        fi

        # Check if the configuration file exists and has the 'system_user' key
        CONFIG_FILE="$SCRIPT_DIR/../src/Config/server.php"
        if [ ! -f "$CONFIG_FILE" ] || ! php -r "\$config = require('$CONFIG_FILE'); exit(isset(\$config['system_user']) ? 0 : 1);" ; then
            print_message "33;1" "⚠️" " Warning: Configuration file $CONFIG_FILE not found or does not contain 'system_user'."
            print_message "33;1" "ℹ️" "  Please run lhd-install.sh to set up the system configuration properly."
            exit 1
        fi

        # Change ownership of the directory
        chown :www-data "$DIRECTORY_PATH"
        print_message "32;1" "✅" " Ownership of '$DIRECTORY_PATH' changed to group 'www-data'."
    else
        print_message "31;1" "❗" " Error: Directory '$DIRECTORY_PATH' does not exist."
        exit 1
    fi
else
    print_message "33;1" "ℹ️" "  Note: No directory path provided. Please ensure that the directory set in the project configuration is in the 'www-data' group with 'chown :www-data /path/directory/project'."
fi

print_message "32;1" "✅" " Deployment structure for project '$PROJECT_NAME' has been created."