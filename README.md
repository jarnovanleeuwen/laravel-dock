[![pipeline status](https://gitlab.com/jarnovanleeuwen/laravel-dock/badges/master/pipeline.svg)](https://gitlab.com/jarnovanleeuwen/laravel-dock/pipelines)

Learning the concepts of [Docker](https://www.docker.com/), I created an example Laravel application using Docker's best architectural and security practices. The boilerplate code in this repository provides:

- Good practices on security (not running as root, no passwordless databases, no unnecessary file permissions).
- Using official Apache, PHP 8, MySQL 8 and Redis 6 images.
- A single code base for **development and production environments**.
- A single `.env` configuration file.
- A slim `Dockerfile` using only **official images**.
- Tests structured for your CI/CD pipeline.
- A convenient binary providing `up`, `artisan`, `exec`, `push`, and even `deploy` commands.
- Deployments to **Kubernetes** using the commit hash for easy rollbacks or staging.
- Built-in support for Laravel's key concepts: **scheduling, queues, cache etc.**
- Built-in Laravel Horizon for managing queue workers through configuration.
- All configuration in source control (e.g. virtual hosts, OPcache, InnoDB parameters).
- Integrated with **GitHub Actions** for automated testing and publishing of images.

# Installation

*Docker Engine version 18.06.0 or higher is required.*

- Install [Docker Desktop for Mac](https://hub.docker.com/editions/community/docker-ce-desktop-mac) (or the equivalent for your operating system)
- Clone this repository
- Copy `.env.example` to `.env` and edit the file to match your environment.
- Run `./dock up`
- Run `./dock exec composer install`
- Run `./dock artisan migrate`
- Visit http://localhost/status

# Usage

The stack can be managed through the `dock <command>` command-line executable. It supports the following commands.

| Command | Description |
|---------|-------------|
| `up` | Start the local stack (webserver, database) for development. |
| `down` | Stop the local stack. |
| `restart` | Restart the local stack. |
| `scheduler` | Start Laravel's scheduler daemon. |
| `queue` | Start Laravel Horizon (queue workers). |
| `tail` | Tail and follow the Laravel logs. |
| `build [tag]` | Build and tag an image ready for production. |
| `push [tag]` | Push the latest image to the container registry defined in `REGISTRY`. |
| `deploy [tag]` | Deploy to Kubernetes |
| `exec [...]` | Run arbitrary commands inside the running application container. For example: `dock exec bash` to open an interactive shell in the running app container. |
| `kubectl [...]` | Run `kubectl` with the context defined in `KUBERNETES_CONTEXT`. |
| `test [...]` | Run `phpunit` inside the running application container. For example: `dock test --filter ExampleTest`. |
| `artisan [...]` | Run `artisan` inside the running application container. For example: `dock artisan tinker`. |
| `<any>` | Will be passed to `docker-compose`. For example: `dock ps`. |

By default, Apache binds to port 80, MySQL to port 3306 and Redis to port 6379. This can be changed by modifying `HOST_PORT_HTTP`, `HOST_PORT_HTTPS`, `HOST_PORT_DB` or `HOST_PORT_REDIS`.

# Kubernetes

You can deploy the Laravel application, including MySQL, Redis, Horizon and a scheduler by applying the [`kubernetes.yaml`](https://github.com/jarnovanleeuwen/laravel-dock/blob/master/build/kubernetes.yaml) config. The example assumes that you are using external (managed) services for MySQL and Redis, but this can be modified to run your own containers using persistent volumes. 

First, create secrets for the Docker registry and application keys and passwords.
```sh
./dock kubectl create secret docker-registry regcred --docker-server=<Registry server> --docker-username=<Username> --docker-password=<Password>
./dock kubectl create secret generic app-secrets --from-literal=APP_KEY='<256 bit key>' --from-literal=DB_PASSWORD='<MySQL password>' --from-literal=REDIS_PASSWORD='<Redis password>'
```

Then, deploy the application.
```sh
./dock deploy [tag]
```

Finally, you can run the migrations or any other artisan command.
```sh
./dock kubectl exec -it service/web -- php artisan migrate
```

# GitHub Actions
See push.yml for an example workflow that builds the image, starts the application, runs the tests and publishes the image to the GitHub Container Registry. Images are tagged with the SHA-hash of the commit that triggered the build. By default, the image is only published for builds in the `main` branch. However, the image is also published in other branches when the commit message includes the string `/publish`.