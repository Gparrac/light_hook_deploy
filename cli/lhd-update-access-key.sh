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

# Check if keys.php exists
if [ ! -f "$KEYS_FILE" ]; then
    echo "Error: keys.php file does not exist."
    exit 1
fi

# Check if the project exists
if ! grep -q "'$PROJECT_NAME'" "$KEYS_FILE"; then
    echo "Error: Project '$PROJECT_NAME' does not exist."
    exit 1
fi

# Hash the new password using PHP
HASHED_PASSWORD=$(php -r "echo password_hash(addslashes('$PASSWORD'), PASSWORD_DEFAULT);")

# Update the project with the new hashed password in keys.php
php -r "
\$file = '$KEYS_FILE';
\$array = include \$file;
if (isset(\$array['$PROJECT_NAME'])) {
    \$array['$PROJECT_NAME'] = '$HASHED_PASSWORD';
    file_put_contents(\$file, '<?php return ' . var_export(\$array, true) . ';');
    echo 'Project \"$PROJECT_NAME\" password has been updated.' . PHP_EOL;
} else {
    echo 'Error: Project \"$PROJECT_NAME\" does not exist.' . PHP_EOL;
    exit(1);
}
"