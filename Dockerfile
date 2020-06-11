FROM composer as composer
ADD . /app
WORKDIR /app
RUN composer install --no-dev

FROM php

COPY --from=composer /app /app

WORKDIR /app
CMD ["php", "mail.php"]