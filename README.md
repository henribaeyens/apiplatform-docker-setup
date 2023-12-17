# Prerequisite

You'll need to clone and setup the development-kit package to set up your development environnement.

```
git clone https://github.com/neimheadh/development-kit.git
cd development-kit
bin/setup
```
Refer to the README for more info on how to use this kit.

# About

This is really a barebones installation for an api-platform project (with Symfony 6.4) for both reference and experimentation.  
The only things I've added so far are the mailer service and a very simple message handler.   

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

# Starting up

```
git clone https://github.com/henribaeyens/apiplatform-docker-setup.git
cd apiplatform-docker-setup
```
Check that the files in the bin directory are executable.  
Build the project using the following:

```
bin/build
```

## What the build does

- set up the containers
- install packages using composer
- create the test database if it does not exist
- run the migrations for both the **test** and **dev** environments
- generate the jwt keys if they do not exist
- install npm packages necessary to run the React admin

# Testing

Tests are done using pest. Read about the pest testing framework at https://pestphp.com/docs/  
A few very basic tests have been implemented.  

Run the tests with the following command:
```
bin/bash # connects to the php container
./vendor/bin/pest
```
or
```
bin/test
```

### Test 1
Send an email.   
You can navigate to https://mail.api.docker.localhost to check that the email is in the mailbox
### Test 2
Dispatch a message to the RabbitMQ broker.   
You can check the RabbitMQ management interface at https://rmq.api.docker.localhost to see if it is there.   
Invoke the following command to consume the message:
```
bin/bash # access the php container
bin/console messenger:consume -vv
```
There should be three messages queued: the message sent with test 2 and one for each user created with the fixtures loaded during the build process.
### Test 3
An authentification attempt fails.
### Test 4
An authentication succeeds

