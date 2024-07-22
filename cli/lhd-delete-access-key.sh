#!/bin/bash

# Check the number of arguments
if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <project_name>"
    exit 1
fi

PROJECT_NAME=$1
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

# Remove the project from keys.php
php -r "
\$file = '$KEYS_FILE';
\$array = include \$file;
if (isset(\$array['$PROJECT_NAME'])) {
    unset(\$array['$PROJECT_NAME']);
    file_put_contents(\$file, '<?php return ' . var_export(\$array, true) . ';');
    echo 'Project \"$PROJECT_NAME\" credentials have been deleted.' . PHP_EOL;
} else {
    echo 'Error: Project \"$PROJECT_NAME\" does not exist.' . PHP_EOL;
    exit(1);
}
"