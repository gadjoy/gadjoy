FROM wordpress:6.4

# Set PHP configurations to 32GB
RUN echo "upload_max_filesize=32G" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size=32G" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit=32G" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_input_time=300" >> /usr/local/etc/php/conf.d/custom.ini

# Copy the plugin source into a temp location in the image
COPY All-In-One-WP-Migration-With-Import-master /plugin-src/all-in-one-wp-migration

# Set permissions in the image (for initial build)
RUN chown -R www-data:www-data /plugin-src/all-in-one-wp-migration \
    && chmod -R 755 /plugin-src/all-in-one-wp-migration

# Add entrypoint script to copy plugin into the volume at container start
COPY copy-plugins.sh /copy-plugins.sh
RUN chmod +x /copy-plugins.sh

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Add a script to generate a WordPress password hash
COPY wp-generate-pass.php /var/www/html/wp-generate-pass.php

ENTRYPOINT ["/copy-plugins.sh"] 