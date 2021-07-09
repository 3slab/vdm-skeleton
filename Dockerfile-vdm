FROM 3slab/vdm-library-base:3.x

RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony

COPY . /var/www/html

RUN cd /var/www/html \
  && composer install -n \
  && composer clearcache -n
