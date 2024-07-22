#!/bin/bash

# Check if script is run as root
if [ "$(id -u)" -ne 0 ]; then
    echo "This script must be run as root"
    exit 1
fi

# Check if the username parameter is provided
if [ -z "$1" ]; then
    echo "Usage: $0 <username>"
    exit 1
fi

USERNAME=$1
SERVICE_PATH=$(dirname $(dirname $(realpath $0)))
SCRIPTS_PATH="$SERVICE_PATH/scripts"

# User creation or verification
echo "* User creation or verification"
if id "$USERNAME" >/dev/null 2>&1; then
    echo "  - User $USERNAME already exists"
else
    useradd --create-home --shell /bin/bash $USERNAME
    if [ $? -ne 0 ]; then
        echo "  - Failed to create user $USERNAME"
        exit 1
    fi
    passwd -d $USERNAME
    echo "  - User $USERNAME has been created"
fi

# Adding user to www-data group
echo "* Adding user to www-data group"
if id -nG "$USERNAME" | grep -qw "www-data"; then
    echo "  - $USERNAME is already in the www-data group"
else
    usermod -aG www-data $USERNAME
    echo "  - $USERNAME has been added to the www-data group"
fi

# Validating Scripts directory
echo "* Validating Scripts directory"
if [ -d "$SCRIPTS_PATH" ]; then
    echo "  - Scripts directory exists"
else
    echo "  - Scripts directory does not exist. Please create it at $SCRIPTS_PATH"
    exit 1
fi

# Configuring sudoers
echo "* Configuring sudoers"
echo "$USERNAME ALL=(ALL) NOPASSWD: $SCRIPTS_PATH/*" | tee /etc/sudoers.d/$USERNAME

# Setting permissions for the service directory
echo "* Setting permissions for the service directory"
chown -R $USERNAME:www-data $SERVICE_PATH

# Checking FTP access
echo "* Checking FTP access"
FTP_OPEN=$(netstat -tuln | grep ':21' | grep -w "$USERNAME")
if [ -n "$FTP_OPEN" ]; then
    echo "  - Warning: FTP is open for $USERNAME. It is recommended to close FTP access for security reasons."
fi

echo "Installation completed."