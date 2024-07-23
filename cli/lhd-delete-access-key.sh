#!/bin/bash

print_message() {
    COLOR=$1
    MESSAGE=$2
    echo -e "\n\e[${COLOR}m${MESSAGE}\e[0m\n"
}

if [ "$#" -ne 1 ]; then
    print_message "31;1" "❗ Usage: $0 <project_name>"
    exit 1
fi

PROJECT_NAME=$1
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

# Remove the project from keys.php
php -r "
\$file = '$KEYS_FILE';
\$array = include \$file;
if (isset(\$array['$PROJECT_NAME'])) {
    unset(\$array['$PROJECT_NAME']);
    file_put_contents(\$file, '<?php return ' . var_export(\$array, true) . ';');
    echo \"\n\033[32;1m✅ Project '$PROJECT_NAME' credentials have been deleted.\033[0m\n\n\";
} else {
    echo \"\n\033[31;1m❗ Error: Project '$PROJECT_NAME' does not exist.\033[0m\n\n\";
    exit(1);
}
"