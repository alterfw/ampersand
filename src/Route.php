<?php

namespace Ampersand;

use Ampersand\Http\Request;
use Ampersand\Http\Response;
use Ampersand\Callback;
use Ampersand\Bootstrap;

class Route {

  private $routes = [];
  private $root = false;
  private $base = '';
  private static $routeInstance;
  private $prefix = '';
  private $middlewares = [];

  private static function instance() {
    if(!self::$routeInstance){
      self::$routeInstance = new Route();
    }
    return self::$routeInstance;
  }


  // --- Public API

  public static function getRoutes(){
    return self::instance()->routes;
  }

  public static function reset() {
    if(self::instance()->getEnv() != 'TEST') throw new Exception("reset() shound't be called in production!");
    return self::instance()->setRoutes([]);
  }

  public static function group() {
    self::instance()->addGroup(func_get_args());
  }

  public static function get(){
    self::instance()->addRoute('GET', func_get_args());
  }

  public static function post(){
    self::instance()->addRoute('POST', func_get_args());
  }

  public static function put(){
    self::instance()->addRoute('PUT', func_get_args());
  }

  public static function delete(){
    self::instance()->addRoute('DELETE', func_get_args());
  }

  public static function map() {
    $parameters = func_get_args();
    $methods = $parameters[0];
    if(!is_array($methods)) throw new \Exception("The first argument for map() method must be an array");
    unset($parameters[0]);
    $args = array_values($parameters);
    self::instance()->addRoute($methods, $args);
  }


  // --- Private methods

  private function __construct(){
    $this->setBase();
    $this->registerFilters();
  }

  public function setRoutes($r) {
    $this->routes = $r;
  }

  private function setBase() {
    if($this->getEnv() == 'WP')
      $this->base = get_bloginfo('url');
  }

  private function getPrefix($route) {

    $pref = $this->prefix;

    if($pref == '')
      return $route;

    if(strpos($route, '/') !== 0)
      $pref .= '/';

    if(strpos($this->prefix, '/') !== 0)
      $pref = '/'.$pref;

    return $pref.$route;

  }

  private function addGroup($parameters) {

    $callback = $parameters[count($parameters) -1];
    $middlewares = [];

    if(count($parameters) > 2) {
      for($i = 1; $i <= count($parameters) - 2; $i++){
        array_push($middlewares, $parameters[$i]);
      }
    }

    $this->prefix = $parameters[0];
    $this->middlewares = $middlewares;
    call_user_func($callback);
    $this->prefix = '';
    $this->middlewares = [];

  }

  private function addRoute($method, $parameters) {

    $methods = (is_array($method)) ? $method : [$method];
    $_route = $this->getPrefix($parameters[0]);
    $broken = array_values(array_filter(explode('/', $_route)));
    $robj = [];
    $robj['methods'] = $methods;
    $robj['callback'] = $parameters[count($parameters) -1];

    foreach($broken as $part){
      if(preg_match('/^:[a-zA-Z0-9]+/', $part))
        $_route = str_replace($part, "{".substr($part, 1)."}", $_route);
    }

    $robj['route'] = $_route;
    $robj['id'] = str_replace('=', '', base64_encode(implode('', $methods). $robj['route']));
    $robj['middlewares'] = $this->middlewares;

    if(count($parameters) > 2) {
      for($i = 1; $i <= count($parameters) - 2; $i++){
        array_push($robj['middlewares'], $parameters[$i]);
      }
    }

    array_push($this->routes, $robj);
    $this->flush();

  }

  private function getRoute($route_id) {
    foreach($this->routes as $route){
      if($route['id'] == $route_id)
      return $route;
    }
  }

  private function runCallback($cb, $query_vars, $res){

    if(is_object($cb) && ($cb instanceof \Closure)){

      $res->bindAndCall($cb);

    } else if(is_string($cb)) {
      ob_start();
      call_user_func_array($cb, [$res->request, $res]);
      $res->write(ob_get_clean());
    }

  }

  private function getCallback($id, $handler, $query_vars){

    $req = new Request();
    $req->setVars($query_vars);
    $res = new Callback($req, $query_vars);
    $route = $this->getRoute($id);
    if(count($route['middlewares']) > 0){
      foreach($route['middlewares'] as $mid){
        $this->runCallback($mid, $query_vars, $res);
      }
    }

    $this->runCallback($handler, $query_vars, $res);

    Bootstrap::getInstance()->setRequest($req);
    Bootstrap::getInstance()->setResponse($res);

  }

  private function flush() {
    if($this->getEnv() == 'WP') $this->flush_rewrite_rules();
  }

  private function registerFilters() {
    if($this->getEnv() == 'WP') {

      add_filter('generate_rewrite_rules', [$this, 'rewrite_url']);
      add_filter('init',  [$this, 'flush_rewrite_rules']);
      add_action("parse_request", [$this, 'parse_request']);

    }
  }

  public function flush_rewrite_rules() {
    $rules = $GLOBALS['wp_rewrite']->wp_rewrite_rules();
    $need = false;
    $counter = 0;
    if($rules)
    foreach($rules as $key=>$value) {
      if($value === "index.php?ampersand_load=true") $counter++;
    }
    if($counter != count($this->routes)) $need = true;
    if($need) {
      global $wp_rewrite;
      $wp_rewrite->flush_rules();
    }

  }

  public function rewrite_url( $wp_rewrite ) {
    $new_rules = [];
    foreach($this->routes as $route) {
      if($route['route'] == "/") continue;
      $broken = array_values(array_filter(explode('/', $route['route'])));
      $new_rules[$broken[0].'/?'] = 'index.php?ampersand_load=true';
    }
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    return $wp_rewrite->rules;
  }

  private function getEnv() {
    if(defined('ALTER_CLI_RUNNER')) return 'CLI';
    $by_env = getenv("AMPERSAND_ENV");
    if(!empty($by_env) && !defined('AMPERSAND_ENV')) define('AMPERSAND_ENV', $by_env);
    return defined('AMPERSAND_ENV') ? AMPERSAND_ENV : 'WP';
  }

  private function end(){
    if($this->getEnv() == 'WP')
      die(0);
  }


  // --- WordPress API

  public function parse_request() {

    $self = $this;

    $dispatcher = \FastRoute\simpleDispatcher(function($r) use ($self) {
      foreach($self->routes as $route) {
        $r->addRoute($route['methods'], $route['route'], $route['callback'], $route['id']);
      }
    }, [
      'routeCollector' => '\Ampersand\\Collector',
    ]);

    // Fetch method and URI from somewhere
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if(strpos($uri, '/wp-admin') > -1) return;

    if($uri == '/404' || $uri == '/404/')
      http_response_code(404);

    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    switch ($routeInfo[0]) {
      case \FastRoute\Dispatcher::NOT_FOUND:
        if($uri != '/404' && $uri != '/404/') {
          header("HTTP/1.0 404 Not Found");
          header('Location: '.$this->base.'/404');
          die();
        }
        break;
      case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
      case \FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $this->getCallback($handler[1], $handler[0], $vars);
        break;
    }

    Bootstrap::getInstance()->run();
    $this->end();

  }

}
