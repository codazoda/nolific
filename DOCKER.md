# Docker Instructions

These are some instructions on building Nolific into a Docker container. Note that this doesn't work yet because the sqlite data is in the app directory and for Docker containers we need to move that to a volume.

## Build the Docker image

`docker build --no-cache --tag nolific .`

## Create and start the container

```
docker run \
    --hostname nolific \
    --publish "8001:8001" \
    --volume "" \
    --name nolific \
    --detach \
    nolific \
    /usr/bin/php -S 0:8001 -t /var/www
```
