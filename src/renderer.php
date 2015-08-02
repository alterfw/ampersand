<?php

class Render {

  public static function template($template, $data = [], $show = true) {
    $rend = new Renderer();
    if($show) {
        echo $rend->twig()->render(str_replace('.', '/', $template).'.html', $data);
    } else {
      return $rend->twig()->render(str_replace('.', '/', $template).'.html', $data);
    }

  }

  public static function json($data) {
    header('Content-Type: application/json');
    return json_encode($data);
  }

}

class Renderer {

  private $twig;

  public function twig(){
    Twig_Autoloader::register();
    $cache = Config::get('cache') ? __DIR__.'/../../../../'.Config::get('cache').'/' : false;
    $loader = new Twig_Loader_Filesystem(__DIR__.'/../../../../'.Config::get('views').'/');
    $this->twig = new Twig_Environment($loader, array(
        'cache' => $cache
    ));
    return $this->twig;
  }

}
