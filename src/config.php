<?php

namespace Ampersand;

class Config {

  public static function all(){
    $default_conf = require '../config.php';
    $user_config = __DIR__ . '/../../../../config.php';
    $conf_user = [];
    if(file_exists($user_config)) $conf_user = require $user_config;
    $conf = array_merge($default_conf, $conf_user);
    return $conf;
  }

  public static function get($key) {
    $conf = self::all();
    return $conf[$key];
  }

}
