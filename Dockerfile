# Use the alpine base image (small)
FROM alpine:latest

# Copy just the index file into place
COPY / /var/www/
COPY files/ /

# Install php
RUN apk update && \
    apk add php && \
    apk add php-sqlite3 && \
    apk add apache2 && \
    apk add php-apache2

# Environment variables we want in the container
ENV NOLIFIC_DATA /data

# Ports we want in the container
EXPOSE 80

# Run the php dev server (cheating)
CMD /usr/sbin/httpd -DFOREGROUND
