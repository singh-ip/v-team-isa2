FROM devopsfnl/image:php-8.2.11-npx

COPY php.ini /usr/bin

ENTRYPOINT ["/var/www/html/dockerfiles/api-runner"]
