#!/bin/bash

SCRIPT_DIR=$(dirname "$(realpath "$0")")
DEPLOYMENTS_FILE="$SCRIPT_DIR/../deployments/deployments.php"

print_message() {
    COLOR=$1
    MESSAGE=$2
    echo -e "\n\e[${COLOR}m${MESSAGE}\e[0m\n"
}

# Check if deployments.php exists
if [ ! -f "$DEPLOYMENTS_FILE" ]; then
    print_message "31;1" "❗ Error: Deployments file does not exist: $DEPLOYMENTS_FILE"
    exit 1
fi

print_message "38;5;214" "📂 Deployments Projects:"

php -r "
    \$deployments = require('$DEPLOYMENTS_FILE');
    if (empty(\$deployments)) {
        echo \"\033[33;1m🔍 No deployments found.\033[0m\n\n\";
    } else {
        foreach (\$deployments as \$project => \$directory) {
            echo \"\033[32;1m🚀 \$project => \$directory\033[0m\n\";
        }
        echo \"\n\";
    }
"