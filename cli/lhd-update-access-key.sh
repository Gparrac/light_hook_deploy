#!/bin/bash

print_message() {
    COLOR=$1
    MESSAGE=$2
    echo -e "\n\e[${COLOR}m${MESSAGE}\e[0m\n"
}

if [ "$#" -lt 2 ]; then
    print_message "31;1" "❗ Error: Usage: $0 <project_name> <password>"
    exit 1
fi

PROJECT_NAME=$1
PASSWORD=$2
SCRIPT_DIR=$(dirname "$(realpath "$0")")
KEYS_FILE="$SCRIPT_DIR/../src/Config/keys.php"

# Check if keys.php exists
if [ ! -f "$KEYS_FILE" ]; then
    print_message "31;1" "❗ Error: keys.php file does not exist."
    exit 1
fi

# Check if the project exists
if ! grep -q "'$PROJECT_NAME'" "$KEYS_FILE"; then
    print_message "31;1" "❗ Error: Project '$PROJECT_NAME' does not exist."
    exit 1
fi

# Hash the new password using PHP
HASHED_PASSWORD=$(php -r "echo password_hash(addslashes('$PASSWORD'), PASSWORD_DEFAULT);")

# Update the project with the new hashed password in keys.php
UPDATE_RESULT=$(php -r "
\$file = '$KEYS_FILE';
\$array = include \$file;
if (isset(\$array['$PROJECT_NAME'])) {
    \$array['$PROJECT_NAME'] = '$HASHED_PASSWORD';
    file_put_contents(\$file, '<?php return ' . var_export(\$array, true) . ';');
    echo '✅ Project \"$PROJECT_NAME\" password has been updated.';
} else {
    echo '❗ Error: Project \"$PROJECT_NAME\" does not exist.';
    exit(1);
}")

# Print the update result
if [[ $UPDATE_RESULT == *"updated"* ]]; then
    print_message "32;1" "$UPDATE_RESULT"
else
    print_message "31;1" "$UPDATE_RESULT"
fi