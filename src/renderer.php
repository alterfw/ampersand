<?php

use Ampersand\Config;

class Render {

  private static $twig;

  public static function getTwig() {
    if(!self::$twig){
      Twig_Autoloader::register();
      $cache = Config::get('cache') ? __DIR__.'/../../../../'.Config::get('cache').'/' : false;
      $loader = new Twig_Loader_Filesystem(__DIR__.'/../../../../'.Config::get('views').'/');
      $twig = new Twig_Environment($loader, array(
          'cache' => $cache,
          'debug' => Config::get('debug')
      ));
      // Add globals
      $twig->addGlobal('session', \Session::getInstance());
      self::$twig = $twig;
    }
    return self::$twig;
  }

  public static function view($template, $data = []) {
    return self::getTwig()->render(str_replace('.', '/', $template).'.html', $data);
  }

  public static function template($template) {
    ob_start();
    get_template_part($template);
    return ob_get_clean();
  }

}
