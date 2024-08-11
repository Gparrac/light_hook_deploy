#!/bin/bash

print_message() {
    COLOR=$1
    ICON=$2
    MESSAGE=$3
    echo -e "\n\e[${COLOR}m${ICON} ${MESSAGE}\e[0m\n"
}

# Ensure correct usage
if [ "$#" -ne 2 ]; then
    print_message "31;1" "❗" "Usage: $0 <project_name> <token>"
    exit 1
fi

PROJECT_NAME=$1
TOKEN=$2
SCRIPT_DIR=$(dirname "$(realpath "$0")")
TOKENS_FILE="$SCRIPT_DIR/../src/Config/tokens.php"

# Create tokens.php if it doesn't exist
if [ ! -f "$TOKENS_FILE" ]; then
    mkdir -p "$(dirname "$TOKENS_FILE")"
    echo "<?php return array ();" > "$TOKENS_FILE"
    print_message "32;1" "✅" " Created $TOKENS_FILE."
fi

# Check if the project already exists
if grep -q "'$PROJECT_NAME'" "$TOKENS_FILE"; then
    print_message "33;1" "⚠" " Warning: Project '$PROJECT_NAME' already exists and is registered."
    exit 1
fi

# Add the new project and token to tokens.php
php -r "
\$file = '$TOKENS_FILE';
\$array = include \$file;
\$array['$PROJECT_NAME'] = '$TOKEN';
file_put_contents(\$file, '<?php return ' . var_export(\$array, true) . ';');
"

print_message "32;1" "✅" " Project '$PROJECT_NAME' has been added with its token."