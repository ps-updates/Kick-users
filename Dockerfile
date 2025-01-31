# Use PHP CLI as the base image
FROM php:8.1-cli

# Set working directory inside the container
WORKDIR /app

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 8080 (for web-based platforms)
EXPOSE 8080

# Start the bot using PHP's built-in server
CMD ["php", "-S", "0.0.0.0:8080", "-t", "."]
