<?php

class Ampersand {

  private $twig;

  private $config = [
    'views' => 'views',
    'cache' => 'cache',
  ];

  public function __construct(){
    Twig_Autoloader::register();
    $this->twigSetup();
  }

  public function set($key, $value) {
    $this->config[$key] = $value;
    $this->twigSetup();
  }

  public function get($key) {
    return $this->config[$key];
  }

  private function twigSetup(){
    $cache = $this->get('cache') ? __DIR__.'/../'.$this->config['cache'].'/' : false;
    $loader = new Twig_Loader_Filesystem(__DIR__.'/../'.$this->config['views'].'/');
    $this->twig = new Twig_Environment($loader, array(
        'cache' => $cache
    ));
  }

  public function render($template, $data = []){
    echo $this->twig->render(str_replace('.', '/', $template).'.html', $data);
  }

}

$Ampersand = new Ampersand();
$Ampersand->set('cache', false);
function render($template, $data = []){
  global $Ampersand;
  $Ampersand->render($template, $data);
}
