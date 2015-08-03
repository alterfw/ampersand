& (ampersand)
=============

A microframework for Wordpress based on [Slim](http://www.slimframework.com/).

## Routes

You can register and handle custom routes with &:

```php
<?php

Route::get('/hello', function(){
  // your code here
});

Route::post('/subscribe/newsletter', function($req, $res){
  $email = $req->params('email'); // Get $_POST['email'];
});

Route::get('/user/:id', function($req, $res){
  $user_id = $req->params('id'); // Get $_GET['id'];
});
```

## Templates

Ampersand uses [Twig](http://twig.sensiolabs.org/) to render templates.

You can render templates using routes:

```php
<?php
Route::get('/hello/:name', function($req, $res){
  $res->render('hello', ['name' => $req->params('name')]); // will render views/hello.html
});
```

Or just render a json:

```php
<?php
Route::get('/hello/:name', function($req, $res){
  $res->toJSON($req->params()); // will render views/hello.html
});
```

You can also use layouts and template blocks, read more in [Twig documentation](http://twig.sensiolabs.org/documentation).

### Wordpress templates

Don't want to use Ampersand templates? Not problem. You can also use Wordpress templates:

```php
<?php

Route::get('/search', function($req, $res){
  $res->template('search'); // Will render your-theme/search.php
});
```

## Models

Ampersand does not include features related to models but it works well with [Hero](https://github.com/alterfw/hero).

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
