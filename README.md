[![pipeline status](https://gitlab.com/jarnovanleeuwen/laravel-dock/badges/master/pipeline.svg)](https://gitlab.com/jarnovanleeuwen/laravel-dock/pipelines)

Learning the concepts of [Docker](https://www.docker.com/), I created an example Laravel application using Docker's best architectural and security practices. The boilerplate code in this repository provides:

- Best practices on security (not running as root, no passwordless databases, no unnecessary file permissions).
- Using official Apache, PHP 7.2, MySQL 5.7 and Redis 5.0 images.
- A single code base for **development and production environments**.
- A single `.env` configuration file.
- A slim `Dockerfile` using only **official images**.
- Tests structured for your CI/CD pipeline.
- A convenient binary providing `up`, `artisan`, `build`, `push`, and even `deploy` commands.
- Deployments to a multi-server cloud environment using **Docker Swarm**.
- Built-in support for Laravel's key concepts: **scheduling, queues, cache etc.**
- Built-in Laravel Horizon for managing queue workers through configuration.
- All configuration in source control (e.g. virtual hosts, OPcache, InnoDB parameters).

# Installation

*Docker Engine version 18.06.0 or higher is required.*

- Install [Docker Desktop for Mac](https://hub.docker.com/editions/community/docker-ce-desktop-mac) (or the equivalent for your operating system)
- Clone this repository
- Copy `.env.example` to `.env` and edit the file to match your environment.
- Run `./dock up`
- Run `./dock artisan migrate`
- Visit http://localhost/status

# Usage

The stack can be managed through the `dock <command>` command-line executable. It supports the following commands.

| Command | Description |
|---------|-------------|
| `up` | Start the local stack (webserver, database) for development. |
| `scheduler` | Start Laravel's scheduler daemon. |
| `queue` | Start Laravel Horizon (queue workers). |
| `down` | Stop the local stack. |
| `tail` | Tail and follow Docker logs from all running containers. |
| `restart` | Restart the local stack. |
| `build` | Build and tag an image ready for production. |
| `push` | Push the latest image to the [Docker Hub](https://hub.docker.com/) defined in `DOCKER_REPOSITORY`. |
| `deploy` | Instructs (over SSH) the server defined in `DEPLOY_SERVER` to deploy the latest build using Docker Swarm. |
| `deploy-migrations` | Run the database migrations on `DEPLOY_SERVER`. The migration files are taken from the currently deployed build. |
| `clean` | Alias for `docker system prune` |
| `exec [...]` | Run arbitrary commands inside the running application container. For example: `dock exec bash` to open an interactive shell in the running app container. |
| `test [...]` | Run `phpunit` inside the running application container. For example: `dock test --filter ExampleTest`. |
| `artisan [...]` | Run `artisan` inside the running application container. For example: `dock artisan tinker`. |
| `<any>` | Will be passed to `docker-compose`. For example: `dock ps`. |

By default, Apache binds to port 80 and MySQL to port 3306. This, and much more, can be changed by modifying the `docker-compose.yml` file found in the `config` directory. Moreover, configuration details specific to the local environment (for example: mounted volumes such as the `src` directory) can be found in `docker-compose.local.yml`. Likewise, configuration details specific to the (multiserver) production environment (such as placement constraints) can be found in `docker-compose.prod.yml`. The local en production configuration files *extend* (and thus share) the configuration details in `docker-compose.yml`.

# Setting up a multiserver environment

Basically, any server with [Docker Swarm](https://docs.docker.com/engine/swarm/) that can be reached over SSH can be used. To give an idea on how easy it is to setup a multiserver environment, I will include the setup steps below. The steps apply to any cloud server provider, such as DigitalOcean or AWS.

- Create an SSH accessible 'manager' server and initialise the swarm: `docker swarm init --advertise-addr={Private IP}`.
- Add web, database and cache servers and join the swarm through the join token (`docker swarm join-token worker`).
- On the manager server, assign labels to the nodes: `docker node update --label-add tag=db <node-id>`. Assigning labels appropriately enables you to control which service is deployed on what servers, because they can be used as placement constraint in the `docker-compose.prod.yml` configuration file.
- On the manager node, login to Docker Hub using `docker login`.
- On the deployment server (or developer machine) specify `DEPLOY_SERVER`.
- 🚀 Execute a rolling deployment using `./dock deploy`.

Notes: this is a simple setup for illustration purposes. In real production environment, an odd number of 3+ manager nodes is recommended for optimal fault tolerance. The same holds for replicating the web and database servers. Moreover, the swarm can be completely isolated from the Internet by only allowing Docker Swarm traffic between nodes and restricting all other traffic. Add a load balancer setup to forward public traffic on ports 80 and 443 to the swarm. Then, only the manager node would need to open port 22 (SSH) to known deployment hosts.

Opening the required ports on swarm nodes in UFW:
```bash
ufw allow 2377/tcp
ufw allow 7946/tcp
ufw allow 7946/udp
ufw allow 4789/udp
ufw reload
```

# CI/CD
The `sut` service in `docker-compose.test.yml` can be used for automated testing. I have experimented with automated builds and tests on [Docker Hub](https://hub.docker.com/) and [GitLab.com](https://about.gitlab.com/product/continuous-integration/)'s CI/CD pipelines.

## DockerHub
In the *Automated builds* configuration section, make sure to set the *Dockerfile location* to `build/Dockerfile` in your build rules.

## GitLab.com
The build, test and release CI/CD pipeline stages are defined in `.gitlab-ci.yml`.

## Other
To  1) start the required services (i.e. MySQL), 2) wait for them to be ready, 3) run the database migrations and 4) run `phpunit`:

```bash
docker-compose -f build/docker-compose.test.yml run sut
```