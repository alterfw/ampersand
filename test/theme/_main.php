<?php

use Ampersand\Route;

Route::get('/', function(){
  $this->render('index');
});

Route::get('/hello', function() {
  echo "Hello!";
});

Route::get('/hello/:name', function($name) {
  echo "Hello $name!";
});
