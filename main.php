<?php

class Config {

  public static function get($key) {
    $conf = require 'src/config.php';
    $user_config = __DIR__ . '/../../../config.php';
    if(file_exists($user_config)) $conf = require $user_config;
    return $conf[$key];
  }

}
