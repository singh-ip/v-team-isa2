FROM devopsfnl/image:php-8.2.11-np

ARG PORT
ENV PORT=${PORT}

WORKDIR /var/www/html

COPY . /var/www/html

COPY php.ini /usr/bin

RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

RUN composer install

RUN chmod -R 777 storage bootstrap dockerfiles
RUN chown -R www-data:www-data storage bootstrap dockerfiles

RUN php artisan migrate --force --seed
RUN php artisan cache:clear

RUN composer run ci

RUN composer run pest

RUN apache2-foreground

ENTRYPOINT ["/var/www/html/dockerfiles/ci-runner"]
