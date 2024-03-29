# About

Barebones installation for an api-platform project (Symfony 6.4 and API platform 3.2) for both reference and experimentation.  

# Prerequisite

You'll need to clone and setup the development-kit package to set up your development environnement.

```
git clone https://github.com/neimheadh/development-kit.git
cd development-kit
bin/setup
```
Refer to the README for more info on how to use this kit.

# Services

## srvc_php
Loads up an php-fpm image with version 8.1 of PHP
## srvc_nginx
Loads up an nginx image to serve the api  
The api's base url is https://api.docker.localhost
## srvc_mariadb
Loads up mariadb
## srvc_pma
Loads up phpmyadmin  
Accessible on https://pma.api.docker.localhost
## srvc_mail
Loads up maildev  
Accessible on https://mail.api.docker.localhost
## srvc_rabbitmq
Loads up rabbitmq   
Accessible on https://rmq.api.docker.localhost
## srvc_phpqa
Loads up phpqa (https://github.com/jakzal/phpqa), a docker image allowing us to run code analysis tools.


# Starting up

```
git clone https://github.com/henribaeyens/apiplatform-docker-setup.git
cd apiplatform-docker-setup
```
Check that the files in the bin directory are executable.  
Build the project using the following:

```
make init
```

## What the build does

- set up the containers
- install packages using composer
- create the test database if it does not exist
- run the migrations for both the **test** and **dev** environments
- load the data fixtures in the test environment
- generate the jwt keys if they do not exist

## Rebuilding the project

```
make rebuild
```

# Doctrine migrations

Migrations are performed at build time. The following command can be invoked after the generation of new migrations:
```
make migrate
```

# Loading fixtures
Fixtures are loaded at build time. They can be reloaded with:
```
make load-fixtures
```

# Administration

SonataAdmin is installed.  
An admin user can be created using the following command:
```
bin/bash # connects to the php container
bin/console app:user:create
```
Log in at https://api.docker.localhost/admin/login  


# Testing

Tests are done using pest. The Pest documentation is at https://pestphp.com/docs/  
A few very basic tests have been implemented.  

Run the tests with the following command:
```
make test
```

### Unit tests
#### Test 1
Send an email.   
You can navigate to https://mail.api.docker.localhost to check that the email is in the mailbox
#### Test 2
Dispatch a message to the RabbitMQ broker.   
You can check the RabbitMQ management interface at https://rmq.api.docker.localhost to see if it is there.   
Invoke the following command to consume the message:
```
bin/bash # to connect to container's shell
bin/console messenger:consume -vv
```
### API tests
### Test 1
An authentification attempt fails (wrong credentials).
### Test 2
An authentification attempt fails (non-verified user).
### Test 3
An authentication succeeds with a verified user
### Test 4
An authentication succeeds but a request to get users fail (throws an access denied exception).  
A user who does not have the admin role cannot request other users.
### Test 5
A user registers and is verified.   
This is a two-step process: upon successful registration, the user is sent a verification code via email. The code is then POSTed to the API to verify the user.   
This would typically be done via some front-end interface.


# Code quality analysis
2 tools are available:
## cs-fixer
```
make csfixer
```
## php-stan
```
make phpstan
```

# Makefile commands
The following commands are "context sensitive", which means they can be invoked either from the host shell or the container's (ie. srvc_php)
- make migrate
- make load-fixtures
- make test  

The others are to be invoked from the host.
