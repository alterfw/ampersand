<?php

class Config {

  public static function get($key) {
    $user_config = __DIR__ . '/../../../config.php';
    var_dump($user_config);
    $conf = require 'src/config.php';
    return $conf[$key];
  }

}
