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
```
apiplatform-docker-setup$ ls -al bin
total 80
drwxr-xr-x  12 apple  staff  384 Dec  9 16:45 .
drwxr-xr-x  23 apple  staff  736 Dec 10 15:12 ..
-rw-r--r--   1 apple  staff  626 Dec  9 16:45 .common
-rwxr-xr-x   1 apple  staff  882 Dec  9 16:45 bash
-rwxr-xr-x   1 apple  staff  861 Dec  9 16:45 build
-rwxr-xr-x   1 apple  staff  325 Dec  9 16:45 clean
-rwxr-xr-x   1 apple  staff  492 Dec  9 16:45 console
-rwxr-xr-x   1 apple  staff   89 Dec  9 16:45 install
-rwxr-xr-x   1 apple  staff  119 Dec  9 16:45 restart
-rwxr-xr-x   1 apple  staff  125 Dec  9 16:45 start
-rwxr-xr-x   1 apple  staff  123 Dec  9 16:45 stop
-rwxr-xr-x   1 apple  staff   91 Dec  9 16:45 test

```
Build the containers with

```
bin/build
```
Install packages with
```
bin/install
```

# Testing

Tests are done using pest. Read about the pest testing framework at https://pestphp.com/docs/  
A few tests have been implemented.

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

Another email should be sent.