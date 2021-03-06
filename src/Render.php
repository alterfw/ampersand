<?php

namespace Ampersand;
use Ampersand\Config;
use Ampersand\Http\Session;
use Ampersand\Helpers\URL;
use Ampersand\Helpers\WP;

class Render {

  private static $twig;

  public static function getTwig() {
    if(!self::$twig){
      \Twig_Autoloader::register();
      $cache = Config::get('cache') ? Config::get('cache').'/' : false;
      $loader = new \Twig_Loader_Filesystem(Config::get('views').'/');
      $twig = new \Twig_Environment($loader, array(
          'cache' => $cache,
          'debug' => Config::get('debug')
      ));
      // Add globals
      $twig->addGlobal('session', Session::getInstance());
      $twig->addGlobal('url', new URL());
      $twig->addGlobal('wp', new WP());
      self::$twig = $twig;
    }
    return self::$twig;
  }

  public static function view($template, $data = []) {
    return self::getTwig()->render(str_replace('.', '/', $template).'.html', $data);
  }

  public static function template($template, $data = []) {
    foreach($data as $key => $value) ${$key} = $value;
    ob_start();
    include(locate_template($template.'.php'));
    return ob_get_clean();
  }

}
