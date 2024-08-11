#!/bin/bash

SCRIPT_DIR=$(dirname "$(realpath "$0")")
KEYS_FILE="$SCRIPT_DIR/../src/Config/keys.php"

print_message() {
    COLOR=$1
    MESSAGE=$2
    echo -e "\n\e[${COLOR}m${MESSAGE}\e[0m\n"
}

# Check if keys.php exists
if [ ! -f "$KEYS_FILE" ]; then
    print_message "31;1" "❗ Error: Keys file does not exist: $KEYS_FILE"
    print_message "33;1" "ℹ️  Please, create a new access key for a new project with 'lhd-add-access-key.sh'."
    exit 1
fi

print_message "38;5;214" "📂 Registered Projects: Access Key"

php -r "
    \$keys = require('$KEYS_FILE');
    if (empty(\$keys)) {
        echo \"\033[33;1m🔍 No projects found.\033[0m\n\n\";
    } else {
        foreach (\$keys as \$project => \$hash) {
            echo \"\033[32;1m🌟 \$project\033[0m\n\";
        }
        echo \"\n\";
    }
"