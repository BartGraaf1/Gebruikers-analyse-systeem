# docker-compose-laravel
A pretty simplified Docker Compose workflow that sets up a LEMP network of containers for local Laravel development. You can view the full article that inspired this repo [here](https://dev.to/aschmelyun/the-beauty-of-docker-for-local-laravel-development-13c0).

## Usage

To get started, make sure you have [Docker installed](https://docs.docker.com/docker-for-mac/install/) on your system, and then clone this repository.

If you have already ran everything once, you can use this
`docker-compose up -d`

Next, navigate in your terminal to the directory you cloned this, and spin up the containers for the web server by running `docker-compose up -d --build app`.

- **nginx** - `:80`
- **mysql** - `:3307`
- **php** - `:9000`
- **redis** - `:6379`
- **mailhog** - `:8025` 

Then Run this:

- `docker-compose run --rm composer update`
- `docker-compose run --rm npm run dev`
- `docker-compose run --rm artisan migrate`
- `docker-compose run --rm artisan db:seed`
- `docker-compose up scheduler`

Mocht je phpMyAdmin willen gebruiken om de DB te navigeren
- `docker-compose up phpmyadmin`

php artisan key:generate
Run php artisan migrate --seed to create the database tables and seed the roles and users tables
Run php artisan storage:link


Restart Docker

- `docker-compose down`
- `docker-compose up -d`

Om PhpMyAdmin te draaien, open de `docker-compose.yml` en druk hier op het pijltje naast PhpMyAdmin


Misschien deze nog:
`docker-compose run --rm artisan make:mail WelcomeMail`