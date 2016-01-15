---
title: Routes
---

The Ampersand Route component is built using the WordPress [Rewrite API](https://codex.wordpress.org/Rewrite_API).

## Creating routes

You can create routes simply calling the static methods of the `Route` class:

```php
Route::get('/hello', function(){
  // Here you are!
});
```

You can also specify HTTP methods to routes like GET, POST, PUT, DELETE, PATCH and OPTIONS.

```php
Route::post('/hello', function(){
  // A POST request to /hello
});
```

## Passing parameters to routes

Ampersand will automatically parse any parameters passed to your routes:

```php
Route::get('/car/:model', function($model){
  // do something with $model
});
```

## Multiple HTTP methods in a single route

You can use the `map` method to create a route that can be triggered by more than one HTTP method:

```php
Route::map(['GET', 'POST'], '/contact', function(){
  // Do something here
}
```

# Middlewares

Middlewares allow you to run pieces of code before the route callback be triggered.

You can create a route middleware passing a string with the function name:

```php

function check_auth($req, $res) {
  // do some stuff
}

Route::get('/car/:model', 'check_auth', function($model){
  // do something with $model
});
```

Or via closure:

```php
Route::get('/car/:model', function($model){
  // do some stuff
}, function($model){
  // do something with $model
});
```

You can also create many middlewares as you need:

```php
Route::get('/car/:model', 'function1', 'function2', 'function3', function($model){
  // do something with $model
});
```

## Closures x function names

In Ampersand routes, Closures (callbacks and middlewares) has bindings to a `Callback` object that inherits from `Response`, so you can simply access response methods via `$this`.

```php
Route::get('/hello', function(){
  $this->render('hello');
});
```

Closures receives also URL parameters as method parameters:

```php
Route::get('/hello/:name', function($name){
  echo 'Hi, $name!';
});
```

# Groups

Groups allow you put routes into a specific commom path and specify common middlewares:

```php
Route::group('dashboard', 'auth', function(){

  Route::get('/cars', function($req, $res){
    // do something
  });

});
```

In the example above, the route will be triggered at `/dashboard/cars` and will call the `auth` middleware before call each route. The middleware parameter, like in the routes, is optional.

You can also put parameters on route group:

```php
Route::group('dashboard/:manufacturer', 'auth', function(){

  Route::get('/cars', function($req, $res, $args){
    $manufacturer = $args['manufacturer'];
  });

});
```
