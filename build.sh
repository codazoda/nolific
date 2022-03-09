#!/bin/sh

# Build the docker image and container
docker build --no-cache --tag nolific .

# Stop and remove the old container
docker container stop nolific
docker container rm nolific

# Run the new container
docker run \
    --hostname nolific \
    --publish "8001:80" \
    --volume ~/Nolific:/data \
    --env NOLIFIC_DATA=/data \
    --name nolific \
    --detach \
    nolific
