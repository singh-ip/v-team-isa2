FROM devopsfnl/image:php-8.2.11-np

WORKDIR /var/www/html

COPY . /var/www/html

COPY php.ini /usr/local/etc/php/

ENTRYPOINT ["/var/www/html/dockerfiles/api-runner"]

#RUN composer install --no-progress
#RUN npm install
#RUN npm run build
