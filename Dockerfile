# Use PHP CLI as the base image
FROM php:8.1-cli

# Set working directory inside the container
WORKDIR /app

# Copy project files
COPY . .

# Expose port 8080 (for web-based platforms)
EXPOSE 8080

# Start the bot using PHP's built-in server
CMD ["php", "-S", "0.0.0.0:8080", "-t", "src"]
