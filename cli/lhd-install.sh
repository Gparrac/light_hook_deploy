#!/bin/bash

print_message() {
    COLOR=$1
    ICON=$2
    MESSAGE=$3
    echo -e "\n\e[${COLOR}m${ICON} ${MESSAGE}\e[0m\n"
}

if [ "$(id -u)" -ne 0 ]; then
    print_message "31;1" "❗" "This script must be run as root"
    exit 1
fi

if [ -z "$1" ]; then
    print_message "31;1" "❗" "Usage: $0 <username>"
    exit 1
fi

USERNAME=$1
SERVICE_PATH=$(dirname $(dirname $(realpath $0)))
SCRIPTS_PATH="$SERVICE_PATH/deployments/scripts"
SYSTEM_FILE="$SERVICE_PATH/src/Config/server.php"

# User creation or verification
print_message "33;1" "🔧" "* User creation or verification"
if id "$USERNAME" >/dev/null 2>&1; then
    print_message "32;1" "✅" "  - User $USERNAME already exists"
else
    useradd --create-home --shell /bin/bash $USERNAME
    if [ $? -ne 0 ]; then
        print_message "31;1" "❌" "  - Failed to create user $USERNAME"
        exit 1
    fi
    passwd -d $USERNAME
    print_message "32;1" "✅" "  - User $USERNAME has been created"
fi

# Ensure the user has /bin/bash if it's not www-data
if [ "$USERNAME" != "www-data" ]; then
    if grep -q "^$USERNAME:" /etc/passwd && ! grep -q "^$USERNAME:.*:/bin/bash" /etc/passwd; then
        sed -i "s/^$USERNAME:[^:]*:/&/bin/bash/" /etc/passwd
        print_message "33;1" "🔧" "  - User $USERNAME set to use /bin/bash"
    fi
fi

# Adding user to www-data group
print_message "33;1" "🔧" "* Adding user to www-data group"
if id -nG "$USERNAME" | grep -qw "www-data"; then
    print_message "32;1" "✅" "  - $USERNAME is already in the www-data group"
else
    usermod -aG www-data $USERNAME
    print_message "32;1" "✅" "  - $USERNAME has been added to the www-data group"
fi

# Validating Scripts directory
print_message "33;1" "🔧" "* Validating Scripts directory"
if [ -d "$SCRIPTS_PATH" ]; then
    print_message "32;1" "✅" "  - Scripts directory exists"
else
    print_message "31;1" "❗" "  - Scripts directory does not exist. Please create it at $SCRIPTS_PATH"
    exit 1
fi

# Configuring sudoers
print_message "33;1" "🔧" "* Configuring sudoers"
echo "$USERNAME ALL=(ALL) NOPASSWD: $SCRIPTS_PATH/*" | tee /etc/sudoers.d/$USERNAME

# Setting permissions for the service directory
print_message "33;1" "🔧" "* Setting permissions for the service directory"
chown -R $USERNAME:www-data $SERVICE_PATH

# Checking FTP access
print_message "33;1" "🔧" "* Checking FTP access"
FTP_OPEN=$(netstat -tuln | grep ':21' | grep -w "$USERNAME")
if [ -n "$FTP_OPEN" ]; then
    print_message "33;1" "⚠️" "  - Warning: FTP is open for $USERNAME. It is recommended to close FTP access for security reasons."
fi

# server.php
if [ -f "$SYSTEM_FILE" ]; then
    sed -i "s/^\s*'system_user'.*/    'system_user' => '$USERNAME',/" "$SYSTEM_FILE"
else
    cat <<EOL > "$SYSTEM_FILE"
<?php
return [
    'system_user' => '$USERNAME',
];
?>
EOL
fi

print_message "32;1" "🎉" "Installation completed."