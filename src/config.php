<?php

return [

  'views' => 'views',
  'cache' => false,
  // Debugging
  'debug' => true,
  // Cookies
  'cookies.encrypt' => false,
  'cookies.lifetime' => '20 minutes',
  'cookies.path' => '/',
  'cookies.domain' => null,
  'cookies.secure' => false,
  'cookies.httponly' => false,
  // Encryption
  'cookies.secret_key' => 'CHANGE_ME',
  'cookies.cipher' => MCRYPT_RIJNDAEL_256,
  'cookies.cipher_mode' => MCRYPT_MODE_CBC,
  // HTTP
  'http.version' => '1.1',

];
