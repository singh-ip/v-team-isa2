FROM devopsfnl/image:php-8.2.11-np

COPY php.ini /usr/local/etc/php/

WORKDIR /var/www/html

COPY . /var/www/html

ENTRYPOINT ["/var/www/html/dockerfiles/api-runner"]

#RUN composer install --no-progress
#RUN npm install
#RUN npm run build
