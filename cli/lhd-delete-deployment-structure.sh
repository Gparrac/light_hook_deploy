#!/bin/bash

print_message() {
    COLOR=$1
    MESSAGE=$2
    echo -e "\n\e[${COLOR}m${MESSAGE}\e[0m\n"
}

if [ "$#" -lt 1 ]; then
    print_message "31;1" "❗ Error: Usage: $0 <project_name>"
    exit 1
fi

PROJECT_NAME=$1
SCRIPT_DIR=$(dirname "$(realpath "$0")")
DEPLOYMENTS_DIR="$SCRIPT_DIR/../deployments"
PROJECTS_DIR="$DEPLOYMENTS_DIR/projects"
DEPLOYMENT_FILE="$DEPLOYMENTS_DIR/deployments.php"

# Remove project from deployments.php
if [ -f "$DEPLOYMENT_FILE" ]; then
    if php -r "exit(array_key_exists('$PROJECT_NAME', require('$DEPLOYMENT_FILE')) ? 0 : 1);" ; then
        PHP_CODE=$(php -r "
            \$deployments = require '$DEPLOYMENT_FILE';
            unset(\$deployments['$PROJECT_NAME']);
            echo '<?php return ' . var_export(\$deployments, true) . ';';
        ")
        echo "$PHP_CODE" > "$DEPLOYMENT_FILE"
        print_message "32;1" "✅ Project '$PROJECT_NAME' has been removed from deployments.php"
    else
        print_message "33;1" "⚠️  Warning: Project '$PROJECT_NAME' is not listed in deployments.php"
    fi
else
    print_message "31;1" "❗ Error: deployments.php not found."
    exit 1
fi

# Remove the project-specific file
PROJECT_FILE="$PROJECTS_DIR/$PROJECT_NAME.php"
if [ -f "$PROJECT_FILE" ]; then
    rm "$PROJECT_FILE"
    print_message "32;1" "✅ Project file '$PROJECT_FILE' has been deleted."
else
    print_message "33;1" "⚠️  Warning: No configuration file found for project '$PROJECT_NAME' in $PROJECTS_DIR."
fi

print_message "32;1" "✅ Deletion process completed."