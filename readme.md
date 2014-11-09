MyLifeStyle - open edition
==========================

it's application for save your day activity

Status: executable without tests


We prepare this application on CzechHackathon event.

Who is we?

Honza Cerny https://github.com/chemix
Martin Surovcak https://github.com/msurovcak
Martin Chapcak  https://github.com/manfm
Jakub Bouček https://github.com/jakubboucek
David Pavliska http://pavliska.com


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


### 4) SQL

init SQL from `app/model/db/init.sql`


### 5) Apache

update your file `etc/hosts` and add new line

`127.0.0.1 mylifestyle.local`

apache/virtuals-list

```
<VirtualHost *:80>
    DocumentRoot "/Sites/mylifestyle/www/
    ServerName mylifestyle.local
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

or with watch whole application

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