<?php

class Render {

  public static function template($template, $data = []) {
    $rend = new Renderer();
    echo $rend->twig()->render(str_replace('.', '/', $template).'.html', $data);
  }

  public static function json($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    die();
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
