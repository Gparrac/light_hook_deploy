#!/bin/bash

SCRIPT_DIR=$(dirname "$(realpath "$0")")
TOKENS_FILE="$SCRIPT_DIR/../src/Config/tokens.php"

print_message() {
    COLOR=$1
    MESSAGE=$2
    echo -e "\n\e[${COLOR}m${MESSAGE}\e[0m\n"
}

# Check if tokens.php exists
if [ ! -f "$TOKENS_FILE" ]; then
    print_message "31;1" "❗ Error: Tokens file does not exist: $TOKENS_FILE"
    print_message "33;1" "ℹ️  Please, create a new secret token for a new project with 'lhd-add-secret-token.sh'."
    exit 1
fi

print_message "38;5;214" "📂 Registered Projects: Secret Tokens"

php -r "
    \$tokens = require('$TOKENS_FILE');
    if (empty(\$tokens)) {
        echo \"\033[33;1m🔍 No projects found.\033[0m\n\n\";
    } else {
        foreach (\$tokens as \$project => \$token) {
            echo \"\033[32;1m🌟 \$project => \$token\033[0m\n\";
        }
        echo \"\n\";
    }
"