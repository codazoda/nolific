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
EXPOSE 80

# Run the php dev server (cheating)
CMD /usr/bin/php -d variables_order=EGPCS -S 0:80 -t /var/www/
