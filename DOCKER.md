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
    --volume ~/data:/data \
    --env NOLIFIC_DATA=/data/nolific \
    --name nolific \
    --detach \
    nolific \
    /usr/bin/php -S 0:8001 -t /var/www
```

## Building a Local Docker Container on Git Push

I wanted to build a local docker container every time I pushed to this repo. To do that I put the following code into the `.git/hooks/pre-push` file and made that file executable.

```
#!/bin/sh

remote="$1"
url="$2"

# Build the docker image and container
docker build --no-cache --tag nolific .

# Stop and delete the old container
docker container stop nolific
docker container rm nolific

# Run the new container
docker run \
    --hostname nolific \
    --publish "8001:8001" \
    --volume ~/Nolific:/var/www/data \
    --name nolific \
    --detach \
    nolific \
    /usr/bin/php -S 0:8001 -t /var/www

exit 0
```