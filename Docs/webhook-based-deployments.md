# Webhook-based Deployment ⚡

To get started with webhook-based deployment, follow these initial setup instructions:

1. **Configure the Secret Token:**

In the `cli` folder of your project, run the following command to set up the deployment configuration:

```bash
./lhd-add-secret-token.sh <project_name> <token>
```
- `<project_name>`: Choose a name for your project. This name helps identify which project you are configuring for deployment.
- `<token>`: This is the token you'll use to secure your webhook.

## Usage Example:

```bash
./lhd-add-secret-token.sh my_project "my_secret_token"
```

This command will prompt you to enter the **project name** and the **token**. The token should be added to the webhook settings in GitHub, GitLab, or Bitbucket to authenticate deployment requests.

2. **Add Webhook to Your Repository:**

After setting up the secret token, add a webhook to your repository in GitHub, GitLab, or Bitbucket. Use the token you configured to secure the webhook and specify which events should trigger the deployment.

For detailed instructions on adding webhooks to your repository, refer to the following links:

- [GitHub Webhook Configuration](https://docs.github.com/en/developers/webhooks-and-events/creating-webhooks)
- [GitLab Webhook Configuration](https://docs.gitlab.com/ee/user/project/integrations/webhooks.html)
- [Bitbucket Webhook Configuration](https://support.atlassian.com/bitbucket-cloud/docs/manage-webhooks/)

For a quick setup guide on configuring webhooks with Light Hook Deploy, you can also check out this [detailed setup guide](./webhook-token-setup.md).

Light Hook Deploy will execute the deployment scripts associated with the project when these configured events are triggered.

🔒 Ensure that your webhook is secured with the token to allow only authenticated requests to trigger deployments.

## 📜 Next Steps

To configure your deployments and scripts, continue with the following documentation:

[Configure Deployments and Scripts](./configuring-deployment-scripts.md)
