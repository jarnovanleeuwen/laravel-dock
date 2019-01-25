Learning the concepts of [Docker](https://www.docker.com/), I created an example Laravel application using Docker's best architectural and security practices. The boilerplate code in this repository provides:

- Best practices on security (not running as root, no passwordless databases, no unnecessary file permissions).
- Using official Apache, PHP 7.2, MySQL 5.7 and Redis 5.0 images.
- A single code base for **development and production environments**.
- A single `.env` configuration file.
- A slim `Dockerfile` using only **official images**.
- A convenient binary providing `up`, `artisan`, `build`, `push`, and even `deploy` commands.
- Deployments to a multi-server cloud environment using **Docker Swarm**.
- Built-in support for Laravel's key concepts: **scheduling, queues, cache etc.**

# Installation

*Docker Engine version 18.06.0 or higher is required.*

- Install [Docker Desktop for Mac](https://hub.docker.com/editions/community/docker-ce-desktop-mac) (or the equivalent for your operating system)
- Clone this repository
- Copy `.env.example` to `.env` and edit the file to match your environment.
- Run `./dock up`
- Visit http://localhost/status

# Usage

The stack can be managed through the `dock <command>` command-line executable. It supports the following commands.

| Command | Description |
|---------|-------------|
| `up` | Start the local stack (webserver, database) for development. |
| `scheduler` | Start Laravel's scheduler daemon. |
| `down` | Stop the local stack. |
| `tail` | Tail and follow Docker logs from all running containers. |
| `restart` | Restart the local stack. |
| `build` | Build and tag an image. |
| `push` | Push the latest image to the [Docker Hub](https://hub.docker.com/) defined in `DOCKER_REPOSITORY`. |
| `deploy` | Instructs (over SSH) the server defined in `DEPLOY_SERVER` to deploy the latest build using Docker Swarm. |
| `deploy-migrations` | Run the database migrations on `DEPLOY_SERVER`. The migration files are taken from the currently deployed build. |
| `clean` | Alias for `docker system prune` |
| `exec [...]` | Run arbitrary commands inside the running application container. For example: `dock exec bash` to open an interactive shell in the running app container. |
| `test [...]` | Run `phpunit` inside the running application container. For example: `dock test --filter ExampleTest`. |
| `artisan [...]` | Run `artisan` inside the running application container. For example: `dock artisan tinker`. |
| `<any>` | Will be passed to `docker-compose`. For example: `dock ps`. |

By default, Apache binds to port 80 and MySQL to port 3306.


# To-do

- Integrate Laravel Horizon.
- Automated tests.
- Automated builds.
- Automated deployments.
