#!/bin/zsh

# Change to the directory this script is running from
cd `dirname "$0"`

# Run the PHP development server
php -S localhost:8001 & disown
