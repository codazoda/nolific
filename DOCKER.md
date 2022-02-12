# Docker Instructions

These are some instructions on building Nolific into a Docker container.

Nolific will store it's sqlite databases in the `data/` directory. This directory is excluded from the repo and Nolific will create it and copy a default file there if it doesn't exist. For the container we'll mount this directory to the home directory on the host (`~/Nolific`) so that we can persist the database.

## Build the Docker image

`docker build --no-cache --tag nolific .`

## Create and start the container

```
docker run \
    --hostname nolific \
    --publish "8001:8001" \
    --volume ~/Nolific:/var/www/data \
    --name nolific \
    --detach \
    nolific \
    /usr/bin/php -S 0:8001 -t /var/www
```
