# Use the alpine base image (small)
FROM alpine:latest

# Copy just the index file into place
COPY / /var/www/

# Install php
RUN apk update && \
    apk add php && \
    apk add php-sqlite3

# Environment variables we want in the container
ENV NOLIFIC_DATA /data

# Ports we want in the container
EXPOSE 8001

# Run the php dev server (cheating)
CMD /usr/bin/php -S 0:8001 -t /var/www/
