<h1 align="center">API</h1>
<p align="center">Central API </p>    
  
## Requirement
1. PHP >= 7.2 with native redis extension installed.
2. Redis
3. MySQL 5.7 with `utf8mb4` character sets  
  
## Development with docker

1. **Installation**
    - Download Docker CE: https://docs.docker.com/install/
    - Clone this repository and change working directory into this project
    - For windows user, you should get use `bash` instead of using `cmd`, give a try with [cmder](http://cmder.net/) or `git bash`
    - Setup environment variables:
        ```
        cp .env.example .env
        ```
    - Start docker:
        ```
        ./dcp up
        ```
    - Composer install:
        ```
        ./dcp composer install
        ```
    - Migration:
        ```
        ./dcp a migrate --seed
        ```
    - API installation:
        ```
        ./dcp a api:install
        ```
    - Your project will be available at `localhost:80`, you can easily to change it through `DOCKER_APP_PORT` environment variable.
2. **Control your development environment with docker**
    - MySQL run in docker container with port `3306`, however, to prevent port conflict with host machine we using port `43306`, you can easily to change it through `DOCKER_MYSQL_PORT` environment variable:
    - Redis run in docker container with port `6379`, on host machine is `7379` (`DOCKER_REDIS_PORT`)
#### Default login information:
Email: `admin@api.asia`  
Password: `password`  
  
  
_Enjoy!_
