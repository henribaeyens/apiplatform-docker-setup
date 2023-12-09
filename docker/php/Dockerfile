ARG IMAGE
FROM ${IMAGE}

# Install packages
ARG PACKAGES="git zip wget curl"
ARG ADDITIONAL_PACKAGES="libicu-dev librabbitmq-dev"
RUN apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y ${PACKAGES} ${ADDITIONAL_PACKAGES} \
 && rm -rf /var/lib/apt/lists/*

# download helper script
RUN wget -q -O /usr/local/bin/install-php-extensions https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions \
    || (echo "Failed while downloading php extension installer!"; exit 1)

# install all required extensions
ARG DATABASE_DRIVER
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && install-php-extensions \
    pdo ${DATABASE_DRIVER} intl zip amqp

# Install composer
ARG COMPOSER_VERSION=latest-stable
RUN wget "https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar" \
 && wget "https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar.sha256sum" \
 && sha256sum -c composer.phar.sha256sum \
 && rm composer.phar.sha256sum \
 && mv composer.phar /usr/local/bin/composer \
 && chmod +x /usr/local/bin/composer \
 && composer --version

# Create developer user
ARG UID
ARG GID
RUN groupadd -g ${GID} developer \
 && useradd -g ${GID} -u ${UID} -m developer
USER developer