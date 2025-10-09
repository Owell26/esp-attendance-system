# Use official PHP image with Apache
FROM php:8.2-apache

# Copy project files to container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Enable mysqli extension for database
RUN docker-php-ext-install mysqli

# Expose default HTTP port
EXPOSE 80
