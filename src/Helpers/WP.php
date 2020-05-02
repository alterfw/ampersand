<?php

namespace Ampersand\Helpers;
use Ampersand\Util\ArrayUtil;

class WP {

  public static function call($method, $args = []) {
    return call_user_func($method, $args);
  }

}
