# Manual Deployment via Endpoint 🛠️

For scenarios where you need to manually trigger a deployment, or when integration is handled by other systems, you can use the endpoint-based deployment method.

1. **Set Up Access Keys:**

First, configure the access key for your project using the CLI tool provided in the cli folder:

```bash
./lhd-add-access-key.sh <project_name> <password>
```
- `<project_name>`: Specify the name of your project for which you want to configure deployment.
- `<token>`: Provide a secure password to authorize deployment access.

2. **Make Deployment Requests:**

You can trigger deployments by sending a POST request to the following endpoint:

```ruby
http://light-hook-deploy-example-domain.com/lhd/<tool>
```

Replace light-hook-deploy-example-domain.com with your actual domain or IP address, and `<tool>` with one of the following options:

- `/deploy`
- `/rollback`
- `/all-in-one`

### Request Body

The POST request should include the following JSON body:

```json
{
  "project": "<project_name>",
  "password": "<password>"
}
```

## Example Request

Here’s an example of how to make a request using `curl`:

```bash
petition=$(curl -s -w "HTTPSTATUS:%{http_code}" -X POST https://light-hook-deploy-example-domain.com/lhd/all-in-one \
-H "Content-Type: application/json" \
-d '{
      "project": "project_name",
      "password": "password"
    }')

http_status=$(echo $petition | tr -d '\n' | sed -e 's/.*HTTPSTATUS://')
response_body=$(echo $petition | sed -e 's/HTTPSTATUS\:.*//g')

if [ "$http_status" -ne 200 ]; then
  echo "Deployment failed with status code $http_status"
  echo "Response: $response_body"
  exit 1
else
  echo "Deployment successful"
  echo "Response: $response_body"
fi
```

### Integration with CI/CD Systems

This `curl` request can be incorporated into CI/CD pipelines, such as `bitbucket-pipelines.yml`, GitHub Actions, or GitLab CI/CD. You can use this example to make deployment requests from your CI/CD configuration or any other automation tools you use.

## Endpoint Details

- **Deploy** (`/deploy`): This endpoint triggers a deployment for the specified project. The service will execute the deployment scripts and respond with a JSON indicating the success or failure of the deployment.

- **Rollback** (`/rollback`): Use this endpoint to initiate a rollback. This is necessary if a deployment fails and you need to revert the changes. The service will run all rollback scripts defined for the project.

- **All-in-One** (`/all-in-one`): This endpoint combines both deployment and rollback processes. If the deployment fails, a rollback will be triggered automatically. The JSON response will indicate the overall status: success, partial success (deployment failed but rollback succeeded), or failure.

## 📜 Next Steps

To configure your deployments and scripts, continue with the following documentation:

[Configure Deployments and Scripts](./configuring-deployment-scripts.md)