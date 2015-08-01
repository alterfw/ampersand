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
