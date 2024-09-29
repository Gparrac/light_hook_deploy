# 🚀  Light Hook Deploy

## Introduction 🌟

**Light Hook Deploy** is a microservice designed to streamline **automated deployments** using webhooks. This service is not meant to replace comprehensive tools like **Jenkins**, **CodeDeploy**, **Azure Pipelines**, or other CI/CD methods. Instead, it offers a **lightweight**, **easily configurable** solution for environments that need automated deployment without the overhead of more complex or resource-heavy tools. 

#### 🎯 Why choose Light Hook Deploy?

- Effortless setup with no complex configurations or multiple dependencies.
- Seamless integration with version control systems for smooth deployments.
- Lightweight and avoids overloading your environment with unnecessary tools.

## 📋 Table of Contents

- [Description](#description)
- [Features](#features)
- [Getting Started](#getting-started)
- [Deployment Methods](#deployment-methods)
- [CLI Reference](#cli-reference)
- [Next Steps](#next-steps)
- [CLI Scripts Overview](#cli-scripts-overview)
- [Credits](#credits)

## Description

**Light Hook Deploy** enables **automated deployments** via webhooks from services like **GitHub**, **GitLab**, and **Bitbucket**. By leveraging a webhook-based strategy, this microservice seamlessly integrates with your repositories and **triggers deployment scripts** when specific events occur, such as pushing changes to a particular branch.

Unlike **Jenkins** or **Azure Pipelines**, which often require more elaborate configurations and several dependencies, **Light Hook Deploy** is designed to be:

- 🛠️ **Lightweight** and **easy to use**.
- 🚀 Set up your projects and deployment scripts in just a **few steps**.
- 💡 Ideal for environments that don’t need a larger CI/CD ecosystem.

## Features

- ⚡ Easy Setup - Quickly configure deployments without complex configurations.
- 🔗 Seamless integration with GitHub, GitLab, and Bitbucket repositories.
- 🚀 Designed for small and medium-sized projects that need reliable automation.
- 💡 Optimized for lean environments - Perfect for servers that don't support heavier tools.
- 🌐 Minimal dependencies - Keeps your system clean and easy to maintain.


## Getting Started

### 📋 Prerequisites

Before you begin, make sure your server is set up with the following:

- **PHP 8.2** or **higher**.
- **Composer**: Ensure that Composer is installed for managing PHP dependencies.
- **SSH access** to run deployment scripts.
- A **Git server** or an account on **GitHub**, **GitLab**, or **Bitbucket**.
- `shell_exec` **enabled**: The program uses `shell_exec` to execute scripts, so your PHP configuration must have this function enabled. Ensure that `shell_exec` is not disabled in the `php.ini` file.

### ⚙️ Installation

To install Light Hook Deploy, run the following command using Composer:

```bash
composer create-project pipestream/light-hook-deploy
```
Next, copy the .env.example file to .env to set up your environment configuration:

```bash
cp .env.example .env
```

The `.env` file contains the following configuration settings:

```bash
# Display Slim Errors
DISPLAY_ERROR_DETAILS=true
LOG_ERRORS=true
LOG_ERROR_DETAILS=true

# -------- Request attempts per IP --------
# Redis Configuration for Rate Limiting
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=your_redis_password

# Rate Limit Configuration (to manage request attempts and prevent brute force attacks)
ENABLE_RATE_LIMIT=false # Enable it if you have Redis configured
MAX_ATTEMPTS_PER_DEPLOY=100
RATE_LIMIT_DEPLOY_MINUTES=60
# -----------------------------------------

# Max execution time
MAX_EXECUTION_TIME=600
```

**Important Note**: The **`MAX_EXECUTION_TIME`** variable is crucial for ensuring sufficient execution time for scripts to avoid `502 Gateway Timeout` errors. It is recommended to extend the timeout settings in `Apache` or `Nginx` (or both, if using Nginx as a reverse proxy).

Additionally, there is Redis configuration available to **block IPs** that attempt to execute deployments through trial and error (**brute force**). To enable Redis for rate limiting, set the following variables in your .env file:

```bash
ENABLE_RATE_LIMIT=true # Enable it if you have Redis configured
MAX_ATTEMPTS_PER_DEPLOY=100 # Maximum deploy attempts allowed per hour
RATE_LIMIT_DEPLOY_MINUTES=60 # Time frame in minutes for rate limiting
```
In this configuration, you can have a maximum of 100 deployment attempts per hour.

Once the environment is set up, you need to set the permissions for the cli folder to 755 to allow the use of the command-line system:

```bash
chmod 755 cli
```

Once installed, navigate to the `cli` folder and run the `./lhd-install.sh` script, passing the username as a parameter. This user will be used to execute the deployment scripts from the `./deployments/scripts/` directory.

```bash
cd cli
./lhd-install.sh <username>
```

#### 👤 User Configuration

The `lhd-install.sh` script expects a `<username>` to be passed when running. You can use an existing user, or if needed, the script will guide you through creating a new one. The selected user will be configured to have the necessary permissions for executing the deployment scripts.

- You can assign a user like `www-data` (commonly used by web services), or select a different user to keep deployment tasks independent.

- The script will grant the selected user the following permissions:
    - **Web Service Execution**: The user will have `www-data` privileges for running the service via the web.
    - **Sudo Execution**: Sudo permissions will be restricted to the scripts located in the `scripts` folder within the `deployments` directory.

- The user will be configured in `/etc/passwd` to allow Bash execution, as this is necessary to run deployment scripts. This ensures that the user has the appropriate shell access to execute commands during the deployment process.

#### 🌐 Web Server Configuration

Make sure your web server (Apache or Nginx) is configured to point to the public directory, where the index.php file is located. This is necessary for the proper functioning of the application.

```bash
DocumentRoot /path-to-your-project/public
```
Ensure that your Apache or Nginx configuration file is updated to serve content from this directory.

## Deployment Methods

1. **Webhook-based Deployment** ⚡

This method utilizes webhooks from services like GitHub, GitLab, or Bitbucket. The service can be configured to listen for any event specified in the webhook section of your repository, such as:

- 🚀 Pushes
- 🔀 Merges
- 📩 Pull requests
- ⚙️ Any other custom event you configure.

When these events are triggered, **Light Hook Deploy** executes the deployment scripts that have been configured for the project.

🔒 You can secure the webhook with a token to ensure that only authenticated requests trigger deployments.

[📚 Read more about Webhook-based Deployment](./Docs/webhook-based-deployments.md)

2. **Manual Deployment via Endpoint** 🔐

Alternatively, you can deploy your project by sending a request to a specific deployment endpoint. This method requires you to provide the **project name** and a **password** to authorize the deployment.

This approach is particularly recommended when continuous integration is handled by the same repository or across different servers and tools, and it’s necessary to connect directly with the project for deployment. For example, if you're using:

- 🔧 Bitbucket Pipelines
- ⚙️ GitHub Actions
- 🔄 GitLab CI/CD

or if you have another server configured for continuous integration, this method ensures that the deployment can be handled on the specific host where the project resides.

✅ This option is ideal when your CI/CD setup needs to interact directly with the deployment host, providing more control over when and how deployments are triggered.

[📚 Read more about Manual Deployment via Endpoint](./Docs/webhook-deployment-via-endpoint.md)


## Next Steps

📜  To configure your deployments and scripts effectively, proceed with the following documentation:

- [Configure Deployments and Scripts](./Docs/configuring-deployment-scripts.md): This section will guide you through setting up your webhooks, defining deployment scripts, and ensuring that your integration works smoothly with your version control system. It is essential for making the most out of Light Hook Deploy and ensuring seamless automated deployments.

- [Response Handling and Status Codes](./Docs/api-responses-and-status-codes.md.md): Here, you will learn about how to handle responses and understand the various status codes returned by the deployment endpoints. This knowledge is crucial for troubleshooting and optimizing your deployment processes.

By following these steps, you’ll ensure a robust setup for automated deployments and a clear understanding of how to manage your deployment lifecycle effectively.


## CLI Scripts Overview

📜 To better understand the functionality of the `CLI` scripts within the cli directory, please refer to the following documentation:

- [CLI Scripts Overview](./Docs/command-line-interface.md): This section covers the purpose and functionality of the CLI scripts, such as `lhd-add-deployment-structure.sh`, `lhd-add-secret-token.sh`, and more. These scripts help in configuring deployments, managing access keys, and handling deployment structures effectively. By familiarizing yourself with these scripts, you will be able to manage your deployments efficiently through the command line.

## 👥 Credits

Special thanks to the following contributors for their work on this project:


| [![Crisian Martinez Blanco](https://avatars.githubusercontent.com/u/44187907?v=4&s=100)](https://github.com/Cristian-Blanco) | [![Gabriel Parra](https://avatars.githubusercontent.com/u/65502363?v=4&s=100)](https://github.com/Gparrac) |
|:---:|:---:|
| **[Cristian-Blanco](https://github.com/Cristian-Blanco)** | **[Gparrac](https://github.com/Gparrac)** |