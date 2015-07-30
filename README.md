Blog Post Datamodel
===================

A simple datamodel written in PHP for blog articles, comments, and replies.

## Prerequisites

* PHP 5. Written using 5.5.9, minimum capatible version unknown.
* SQLite3:  https://www.sqlite.org
* Composer: https://getcomposer.org

## Getting started

### Install PHP5 and SQLite3

```bash
$ sudo apt-get install php5-dev
$ sudo apt-get install sqlite3 libsqlite3-dev
```

### Install composer

Follow instructions at: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx

### Clone and install PHPUnit:

```bash
$ git clone https://github.com/timecatalyst/blog-post-datamodel.git
$ cd blog-post-datamodel
$ composer install
```

### Run tests
```bash
$ vendor/bin/phpunit tests/AllTests
```
