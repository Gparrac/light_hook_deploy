#!/bin/bash

SCRIPT_DIR=$(dirname "$(realpath "$0")")
KEYS_FILE="$SCRIPT_DIR/../src/Config/keys.php"

# Check if keys.php exists
if [ ! -f "$KEYS_FILE" ]; then
    echo "Keys file does not exist: $KEYS_FILE"
    exit 1
fi

echo "Registered projects:"
php -r "
    \$keys = require('$KEYS_FILE');
    foreach (\$keys as \$project => \$hash) {
        echo \"- \$project\n\";
    }
"