FROM richarvey/nginx-php-fpm:3.1.6

# Copy the app (the base image WORKDIR is /var/www/html)
COPY . .

# Image config (match example syntax)
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Composer can run as root inside this image
ENV COMPOSER_ALLOW_SUPERUSER 1

# Ensure Node is present for Vite at runtime (so our startup script can build assets)
RUN apk add --no-cache nodejs npm

# Make sure our startup script is executable (so RUN_SCRIPTS=1 will run it)
RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh

# Start the image's supervisor (nginx+php-fpm); it will run scripts/00-*.sh on boot
CMD ["/start.sh"]
