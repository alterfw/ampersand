---
layout: home
---

& (ampersand)
=============

[![Build Status](https://travis-ci.org/alterfw/ampersand.svg)](https://travis-ci.org/alterfw/ampersand)

A microframework for Wordpress based on [Slim](http://www.slimframework.com/).

## Routes

You can register and handle custom routes with &:

```php
<?php

Route::get('/hello', function(){
  // your code here
});

Route::post('/subscribe/newsletter', function(){
  $email = $this->params('email'); // Get $_POST['email'];
});

Route::get('/user/:id', function($id){
  // do something with $id
});
```

## Templates

Ampersand uses [Twig](http://twig.sensiolabs.org/) to render templates.

You can render templates using routes:

```php
<?php
Route::get('/hello/:name', function($name){
  $this->render('hello', ['name' => $name]); // will render views/hello.html
});
```

Or just render a json:

```php
<?php
Route::get('/hello/:name', function($name){
  $res->toJSON($this->params()); // will render views/hello.html
});
```

You can also use layouts and template blocks, read more in [Twig documentation](http://twig.sensiolabs.org/documentation).

### Wordpress templates

Don't want to use Ampersand templates? Not problem. You can also use Wordpress templates:

```php
<?php

Route::get('/search', function(){
  $res->template('search'); // Will render your-theme/search.php
});
```

## Models

Ampersand does not include features related to models but it works well with [Hero](http://alterfw.github.io/hero/).

Just include hero into your theme composer.json

    composer require alterfw/hero

And should work fine:

```php
<?php

$hero = new Hero();
$model = $hero::get();

$books = $model->books->find();
```

Read more about how to use Hero in [Alter's documentation](http://alter-framework.readthedocs.org/en/latest/models.html).

## Configuration

You can override Amperstand default configuration creating a `config.php` in the root of your theme:

```php
<?php

return [
  'views' => 'views',
  'cache' => false
];
```

## Documentation

You can read more about how Ampersand works in the [documentation page](http://alterfw.github.io/ampersand/docs/).


## Contributing

This project doesn't have an styleguide yet but you should follow the existing code. 
Before create any pull requests make sure that all tests are passing.

### Development Environment

To setup de developmente environment first download [Docker](https://www.docker.com/) and create a virtual machine:

    docker-machine create --driver virtualbox default
    eval "$(docker-machine env default)"
    
Then run:

    docker-compose up
    
This will create a WordPress and a theme with Ampersand installed as dependency. 

If you want to modify the theme files, take a look at the `test/theme` directory. These files are copied during the `docker-compose up` command, so if you change anything in these files you need to terminate the process and run again.
