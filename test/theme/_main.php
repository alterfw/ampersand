<?php

Route::get('/', function(){
  echo "Hello!";
});

Route::get('/hello', function() {
  echo "Hello!";
});

Route::get('/hello/:name', function($name) {
  echo "Hello $name!";
});