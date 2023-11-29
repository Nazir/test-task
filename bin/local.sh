#!/usr/bin/env bash

build() {
  docker network create nazir-testtask-network
#   docker-compose --env-file ./compose.env  --file docker-compose.yml up --force-recreate -d  --build
  docker-compose --env-file ./compose.env --file 'docker-compose.dev.yml' up --build --force-recreate --remove-orphans
  docker network create nazir-testtask-network

}

start() {
  docker-compose --env-file ./compose.env  --file docker-compose.yml up  -d
}
stop() {
  docker-compose --env-file ./compose.env  --file docker-compose.yml down
}

case "$1" in
  build)  shift; build "$@" ;;
  start)  shift; start "$@" ;;
  stop)  shift; stop "$@" ;;
  *) print_help; exit 1
esac


# php bin/console doctrine:database:create
# php bin/console doctrine:migrations:migrate

# php bin/console app:references:fill-data --verbose

# php bin/console doctrine:fixtures:load
