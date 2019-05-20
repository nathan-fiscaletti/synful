# Getting Started

## What is Synful?

Synful is a simple PHP framework that gives you the tools to create a custom web API in minutes.

## Prerequisites

Installing Synful is a fairly straight forward process. Below you will find it outlined.

### Requirements

* PHP >=7.1

### Recommended

* Database Server
    - Supported: **MySQL, PostgreSQL, SQLite, SQL Server**

* PHP APCU - [https://www.php.net/manual/en/book.apcu.php](https://www.php.net/manual/en/book.apcu.php)
    - Used for Caching and Rate Limiting

## Installation

### 1. Download Synful

Download a copy of Synful using one of the following methods

**Git**
```sh
git clone -b v2.1.6 https://github.com/nathan-fiscaletti/synful
```

**Wget**
```sh
wget https://github.com/nathan-fiscaletti/synful/archive/v2.1.6.zip
unzip v2.1.6.zip
```

### 2. Set up the filesystem

Move the contents of the repository into your web root and set the owner of the directory to your web servers user. In this instance, we assume your web user is `www-data` and your web root is `/var/www/html`.

```sh
rm -rf /var/www/html
mkdir /var/www/html
chmod -R www-data: /var/www/html
mv ./synful/** /var/www/html/
```

---
Next: [Configuration](./Configuration.md) - ([Back to Index](./README.md))