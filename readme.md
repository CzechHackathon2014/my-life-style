MyLifeStyle - open edition
==========================

it's application for save your day activity

Status: dev version



Installation
------------

### 1) Clone code from GitHub

clone repository `git clone git@github.com:CzechHackathon2014/my-life-style.git`


### 2) Composer

install [Composer](http://getcomposer.org)

download dependencies via composer
`composer.phar install`


### 3) Permissions

write for folders log and temp

`$ chmod -R a+rw temp log`

and for data

`$ chmod -R a+rw www/data`


### 4) SQL

init SQL from `app/model/db/init.sql`


### 5) Apache

update your file `etc/hosts` and add new line

`127.0.0.1 mylifestyle.l`

apache/virtuals-list

```
<VirtualHost *:80>
    DocumentRoot "/Sites/aprila-sandbox/www/
    ServerName mylifestyle.l
    ServerAlias mylifestyle.192.168.1.111.xip.io
</VirtualHost>
```

### 5) Facebook

register new Facebook app and update configuration in app/config/config.local.neon



For development
------------

### Node

install [Node](http://nodejs.org)

download dependencies via npm
`npm install`



### Grunt

install [Grunt](http://gruntjs.com)

build minimalized script file and stylesheets file
`grunt`

build css from stylus
`grunt stylus`

build css from sass
`grunt sass`

autocompile
`grunt watch`


### Bower

yes, we user bower, but for easy development process, we save components to git repository


### Tests

create config file for tests from template /app/config/config.test.neon

run Nette\Tester `vendor/bin/tester tests`

or way with helper file, create in root folder new file "run_tests"

for example if You use XAMPP on Mac:
```
#! /bin/bash
vendor/bin/tester tests -w tests -p /Applications/XAMPP/bin/php
```
or
```
#! /bin/bash
vendor/bin/tester tests -w ./ -p /Applications/XAMPP/bin/php
```

or with clear cache folders before run tests

```
#! /bin/bash
rm -R ./tests/temp/cache
mkdir ./tests/temp/cache
vendor/bin/tester tests -w tests -p /Applications/XAMPP/bin/php
```