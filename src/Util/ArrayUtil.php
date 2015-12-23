<?php

namespace Ampersand\Util;

class ArrayUtil {

  public static function removeEmpty($array) {
    $new = array_filter($array);
    return array_values($new);
  }

  public static function removeIndex($array, $index) {
    unset($array[$index]);
    return array_values($array);
  }

}
