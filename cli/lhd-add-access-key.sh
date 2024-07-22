#!/bin/bash

# Check the number of arguments
if [ "$#" -ne 2 ]; then
    echo "Usage: $0 <project_name> <password>"
    exit 1
fi

PROJECT_NAME=$1
PASSWORD=$2
SCRIPT_DIR=$(dirname "$(realpath "$0")")
KEYS_FILE="$SCRIPT_DIR/../src/Config/keys.php"

# Create keys.php if it doesn't exist
if [ ! -f "$KEYS_FILE" ]; then
    mkdir -p "$(dirname "$KEYS_FILE")"
    echo "<?php return array ();" > "$KEYS_FILE"
fi

# Check if the project already exists
if grep -q "'$PROJECT_NAME'" "$KEYS_FILE"; then
    echo "Project '$PROJECT_NAME' already exists and is registered."
    exit 1
fi

# Hash the password using PHP
HASHED_PASSWORD=$(php -r "echo password_hash('$PASSWORD', PASSWORD_DEFAULT);")

# Add the new project and hashed password to keys.php
php -r "
\$file = '$KEYS_FILE';
\$array = include \$file;
\$array['$PROJECT_NAME'] = '$HASHED_PASSWORD';
file_put_contents(\$file, '<?php return ' . var_export(\$array, true) . ';');
"

echo "Project '$PROJECT_NAME' has been added with its hashed password."