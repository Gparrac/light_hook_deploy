#!/bin/bash

SCRIPT_DIR=$(dirname "$(realpath "$0")")
DEPLOYMENTS_FILE="$SCRIPT_DIR/../deployments/deployments.php"

# Check if DEPLOYMENTS.php exists
if [ ! -f "$DEPLOYMENTS_FILE" ]; then
    echo "Deployments file does not exist: $DEPLOYMENTS_FILE"
    exit 1
fi

echo "Deployments projects:"
php -r "
    \$deployments = require('$DEPLOYMENTS_FILE');
    foreach (\$deployments as \$project => \$directory) {
        echo \"- \$project => \$directory\n\";
    }
"