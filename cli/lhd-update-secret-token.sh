#!/bin/bash

print_message() {
    COLOR=$1
    MESSAGE=$2
    echo -e "\n\e[${COLOR}m${MESSAGE}\e[0m\n"
}

if [ "$#" -lt 2 ]; then
    print_message "31;1" "❗ Error: Usage: $0 <project_name> <token>"
    exit 1
fi

PROJECT_NAME=$1
TOKEN=$2
SCRIPT_DIR=$(dirname "$(realpath "$0")")
TOKENS_FILE="$SCRIPT_DIR/../src/Config/tokens.php"

# Check if tokens.php exists
if [ ! -f "$TOKENS_FILE" ]; then
    print_message "31;1" "❗ Error: tokens.php file does not exist."
    print_message "33;1" "ℹ️  Please add the token for this project using 'lhd-add-secret-token.sh'."
    exit 1
fi

# Check if the project exists
if ! grep -q "'$PROJECT_NAME'" "$TOKENS_FILE"; then
    print_message "31;1" "❗ Error: Project '$PROJECT_NAME' does not exist."
    exit 1
fi

# Update the project with the new token in tokens.php
UPDATE_RESULT=$(php -r "
\$file = '$TOKENS_FILE';
\$array = include \$file;
if (isset(\$array['$PROJECT_NAME'])) {
    \$array['$PROJECT_NAME'] = '$TOKEN';
    file_put_contents(\$file, '<?php return ' . var_export(\$array, true) . ';');
    echo '✅ Project \"$PROJECT_NAME\" token has been updated.';
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