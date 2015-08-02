<?php

use Ampersand\Http\Request;
use Ampersand\Http\Response;

class Route {

  private static $routeInstance;

  private static function instance() {
    if(!self::$routeInstance){
      self::$routeInstance = new RouteImplementation();
    }
    return self::$routeInstance;
  }

  public static function get($route, $callback){
    self::instance()->registerGET($route, $callback);
  }

  public static function post($route, $callback){
    self::instance()->registerPOST($route, $callback);
  }

  public static function put($route, $callback){
    self::instance()->registerPUT($route, $callback);
  }

  public static function delete($route, $callback){
    self::instance()->registerDELETE($route, $callback);
  }

}

class RouteImplementation {

  private $routes = [];
  private $root = false;
  private $base = '';

  public function __construct(){
    $this->base = get_bloginfo('url');
    add_filter('generate_rewrite_rules', [$this, 'rewrite_url']);
    add_filter('query_vars', [$this, 'query_vars']);
    add_filter('init',  [$this, 'flush_rewrite_rules']);
    add_action("parse_request", [$this, 'parse_request']);
  }

  public function __destruct() {
    $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if($this->root && str_replace($this->base, '', $url) == '/'){
      $route = $this->getRoute($this->root);
      $this->getCallback($route, []);
      Ampersand::getInstance()->run();
      exit(0);
    }
  }

  private function addRoute($method, $route, $callback) {

    $robj = [];

    $robj['method'] = $method;
    $robj['callback'] = $callback;
    $robj['id'] = str_replace('=', '', base64_encode($method.$route));

    // Generate the regex url
    $broken = array_values(array_filter(explode('/', $route)));

    if(count($broken) == 1) {
      $robj['regex'] = ''.$broken[0].'/?';
      $robj['qstring'] = 'index.php?amp_route='.$robj['id'];
      $robj['params'] = [];

    } elseif (count($broken) == 0) {
      $this->root = $robj['id'];
      $robj['regex'] = false;
      $robj['qstring'] = false;
      $robj['params'] = [];
    } else {

      $robj['regex'] = '';
      $robj['qstring'] = 'index.php?amp_route='.$robj['id'];
      $robj['params'] = [];
      $br = $broken;
      $pcounter = 0;
      $counter = 0;
      foreach($br as $part){
        $counter++;
        if($counter > 1) $robj['regex'] .= '/';
        if(preg_match('/^:[a-zA-Z0-9]+/', $part)){
          $pcounter++;
          $rpart = str_replace(':', '', $part);
          $robj['regex'] .= '([a-zA-Z0-9]+)';
          array_push($robj['params'], $rpart);
          $robj['qstring'] .= '&'.$rpart.'=$matches['.$pcounter.']';
        } else {
          $robj['regex'] .= $part;
        }

      }
      $robj['regex'] .= '/?';

    }

    array_push($this->routes, $robj);

  }

  private function getRoute($route_id) {
    foreach($this->routes as $route){
      if($route['id'] == $route_id)
      return $route;
    }
  }

  private function getCallback($route, $query_vars){

    unset($query_vars['amp_route']);
    $req = new Request();
    $req->setVars($query_vars);
    $res = new Response();
    $route["callback"]($req, $res);

    Ampersand::getInstance()->setRequest($req);
    Ampersand::getInstance()->setResponse($res);

  }


  public function query_vars( $query_vars ) {
    array_push($query_vars, 'amp_route');
    foreach($this->routes as $route){
      foreach($route['params'] as $param){
        array_push($query_vars, $param);
      }
    }
    return $query_vars;
  }


  public function flush_rewrite_rules() {
    $rules = $GLOBALS['wp_rewrite']->wp_rewrite_rules();
    $need = false;
    foreach($this->routes as $route) {
      if ($route['regex'] && !isset( $rules[$route['regex']])) $need = true;
    }

    if($need){
      global $wp_rewrite;
      $wp_rewrite->flush_rules();
    }

  }

  public function rewrite_url( $wp_rewrite ) {
    $new_rules = [];

    foreach($this->routes as $route) {
      if($route['regex']) $new_rules[$route['regex']] = $route['qstring'];
    }

    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    return $wp_rewrite->rules;
  }

  public function parse_request($wp_query) {

    if (isset($wp_query->query_vars['amp_route'])){
      $route = $this->getRoute($wp_query->query_vars['amp_route']);
      if($route['id'] == $wp_query->query_vars['amp_route'] && $route['method'] == $_SERVER['REQUEST_METHOD']){
        $this->getCallback($route, $wp_query->query_vars);
      } else {
        Ampersand::getInstance()->response()->setStatus(404);
        Ampersand::getInstance()->response()->template('404');
      }

      Ampersand::getInstance()->run();
      exit(0);

    }
  }


  public function registerGET($route, $callback) {
    $this->addRoute('GET', $route, $callback);
  }

  public function registerPOST($route, $callback) {
    $this->addRoute('POST', $route, $callback);
  }

  public function registerPUT($route, $callback) {
    $this->addRoute('PUT', $route, $callback);
  }

  public function registerDELETE($route, $callback) {
    $this->addRoute('DELETE', $route, $callback);
  }

}
