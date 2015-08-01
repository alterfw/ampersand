& (ampersand)
=============

A microframework for Wordpress (in initial development)

## Routes

You can register and handle custom routes with &:

```php
<?php

Route::get('hello', function(){
  // your code here
});

Route::post('subscribe/newsletter', function(){
  // Do something with $_POST
});

Route::get('user/:id', function($user_id){
  // Do something with $user_id
});
```

## Templates

Ampersand uses [Twig](http://twig.sensiolabs.org/) to render templates.

You can set `views` folder and `cache` property to Twig via Ampersand:

```php
<?php

$Ampersand = new Ampersand();
$Ampersand->set('cache', false);
```

And render templates using routes:

```php
<?php
Route::get('hello/:name', function($name){
  render('hello', ['name' => $name]); // will render views/hello.html
})
```

You can also use layouts and template blocks, read more in [Twig documentation](http://twig.sensiolabs.org/documentation).
