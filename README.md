& (ampersand)
=============

[![Build Status](https://travis-ci.org/alterfw/ampersand.svg)](https://travis-ci.org/alterfw/ampersand)

Routes and Views for WordPress Applications, based on [Slim](http://www.slimframework.com/).

## Instalation

    composer require alterfw/ampersand

Require the composer autoload file into your `functions.php`:

```php
require "vendor/autoload.php";
```

## Documentation

You can read more about how Ampersand works in the [documentation page](http://alterfw.github.io/ampersand/docs/).


## Contributing

This project doesn't have an styleguide yet but you should follow the existing code.
Before create any pull requests make sure that all tests are passing.

### Development Environment

To setup de development environment first download [Docker](https://www.docker.com/) and create a virtual machine:

    docker-machine create --driver virtualbox default
    eval "$(docker-machine env default)"

Then run:

    docker-compose up

This will create a WordPress and a theme with Ampersand installed as dependency.

If you want to modify the theme files, take a look at the `test/theme` directory. These files are copied during the `docker-compose up` command, so if you change anything in these files you need to terminate the process and run again.
