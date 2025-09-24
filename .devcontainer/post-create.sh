#!/bin/bash
set -e

# Copy .env.example to .env if .env does not exist
test -f .env || cp .env.example .env

# Install PHP dependencies
composer install

# Install Node dependencies
npm install || true

# Generate Laravel app key
php artisan key:generate || true
