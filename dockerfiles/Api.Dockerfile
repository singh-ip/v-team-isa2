FROM devopsfnl/image:php-8.2.11-npx

#COPY php.ini /usr/local/etc/php/

#ENTRYPOINT ["/var/www/html/dockerfiles/api-runner"]

RUN composer install

RUN composer run dev

RUN git config --global --add safe.directory /var/www/html
RUN git config core.filemode false

RUN npm install
RUN npm run build

RUN apache2-foreground
