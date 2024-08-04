#!/bin/bash

print_message() {
    COLOR=$1
    ICON=$2
    MESSAGE=$3
    echo -e "\n\e[${COLOR}m${ICON} ${MESSAGE}\e[0m\n"
}

# Ensure the script is run with sudo
if [ "$(id -u)" -ne 0 ]; then
    print_message "31;1" "❗" " Error: Please run the script with sudo."
    exit 1
fi

# Ensure correct usage
if [ "$#" -lt 2 ]; then
    print_message "31;1" "❗" " Usage: $0 <project_name> <directory_path>"
    exit 1
fi

PROJECT_NAME=$1
DIRECTORY_PATH=$2
SCRIPT_DIR=$(dirname "$(realpath "$0")")

DEPLOYMENTS_DIR="$SCRIPT_DIR/../deployments"
SYMLINKS_DIR="$DEPLOYMENTS_DIR/symlinks"
PROJECTS_DIR="$DEPLOYMENTS_DIR/projects"
DEPLOYMENT_FILE="$DEPLOYMENTS_DIR/deployments.php"

# Ensure the symlinks directory exists
mkdir -p "$SYMLINKS_DIR"

# Check if the project exists in deployments.php
if ! php -r "\$deployments = require('$DEPLOYMENT_FILE'); exit(isset(\$deployments['$PROJECT_NAME']) ? 0 : 1);" ; then
    print_message "31;1" "❗" " Error: Project '$PROJECT_NAME' not found in $DEPLOYMENT_FILE."
    print_message "33;1" "ℹ️" "  Please run 'lhd-add-deployment-structure.sh' to add the project structure."
    exit 1
fi

PROJECT_FILE=$(php -r "\$deployments = require('$DEPLOYMENT_FILE'); echo \$deployments['$PROJECT_NAME'];")

# Check if the project configuration file exists
if [ ! -f "$DEPLOYMENTS_DIR/$PROJECT_FILE" ]; then
    print_message "31;1" "❗" " Error: Project configuration file '$PROJECT_FILE' does not exist."
    print_message "33;1" "ℹ️" "  Please run 'lhd-add-deployment-structure.sh' to add the project structure."
    exit 1
fi

# Check if the directory exists
if [ ! -d "$DIRECTORY_PATH" ]; then
    print_message "31;1" "❗" " Error: Directory '$DIRECTORY_PATH' does not exist."
    exit 1
fi

# Get the current symbolic link target from the project configuration file
CURRENT_SYMLINK=$(php -r "
\$config = require('$DEPLOYMENTS_DIR/$PROJECT_FILE');
echo \$config['directory'];
")

# Remove existing symbolic link if it exists
if [ -L "$SYMLINKS_DIR/$(basename "$CURRENT_SYMLINK")" ]; then
    rm "$SYMLINKS_DIR/$(basename "$CURRENT_SYMLINK")"
    print_message "33;1" "ℹ️" " Existing symbolic link removed: $SYMLINKS_DIR/$(basename "$CURRENT_SYMLINK")"
fi

# Create a new symbolic link for the project directory
ln -s "$DIRECTORY_PATH" "$SYMLINKS_DIR/$PROJECT_NAME"
print_message "32;1" "✅" " Symbolic link created: $SYMLINKS_DIR/$PROJECT_NAME -> $DIRECTORY_PATH"

# Change ownership of the directory and symbolic link
chown -R :www-data "$DIRECTORY_PATH"
chown :www-data "$SYMLINKS_DIR/$PROJECT_NAME"
print_message "32;1" "✅" " Ownership of '$DIRECTORY_PATH' and '$SYMLINKS_DIR/$PROJECT_NAME' changed to group 'www-data'."

# Update the comment for the directory entry in the project configuration file
UPDATED_CONFIG=$(php -r "
\$config = require('$DEPLOYMENTS_DIR/$PROJECT_FILE');
\$config_code = file_get_contents('$DEPLOYMENTS_DIR/$PROJECT_FILE');
\$config_code = preg_replace('/(directory\'\s*=>\s*\'' . preg_quote(\$config['directory'], '/') . '\',\s*\/\/\s*->\s*).+$/m', '\$1 $DIRECTORY_PATH', \$config_code);
file_put_contents('$DEPLOYMENTS_DIR/$PROJECT_FILE', \$config_code);
")

print_message "32;1" "✅" " Directory comment updated in project configuration file: $PROJECT_FILE"

print_message "32;1" "✅" " Deployment directory for project '$PROJECT_NAME' has been updated."