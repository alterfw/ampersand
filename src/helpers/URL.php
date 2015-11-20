<?php

namespace Ampersand\Helpers;
use Ampersand\Util\ArrayUtil;

class URL
{

  public static function to(){

    $arguments = func_get_args();
    if($arguments[0] == '/') return self::getBaseURL();

    $pieces = ArrayUtil::removeEmpty(explode('/', $arguments[0]));
    if(count($pieces) <= 1) return self::getUrlWithPrefix($arguments[0]);

    $parameters = ArrayUtil::removeIndex($arguments, 0);
    return self::getUrlWithPrefix(self::getReplacements($pieces, $parameters));

  }

  private static function getReplacements($pieces, $arguments) {

    $resultPath = [];
    $argCounter = 0;
    foreach($pieces as $piece) {
      if(preg_match('/^:[a-zA-Z0-9]+/', $piece)){
        array_push($resultPath, $arguments[$argCounter]);
        $argCounter++;
      } else {
        array_push($resultPath, $piece);
      }
    }

    return implode('/', $resultPath);

  }

  private static function getBaseURL() {
    return get_bloginfo('url');
  }

  private static function getUrlWithPrefix($path) {
    $prefix = (substr($path, 0, 1) == '/') ? '' : '/';
    return self::getBaseURL().$prefix.$path;
  }

}
