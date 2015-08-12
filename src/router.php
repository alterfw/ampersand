<?php

use Ampersand\Http\Request;
use Ampersand\Http\Response;
use Ampersand\Callback;

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


  // --- Private methods

  private function __construct(){
    $this->setBase();
    $this->registerFilters();
  }

  public function __destruct() {

    if($this->getEnv() == 'WP') {
      $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      if($this->root && str_replace($this->base, '', $url) == '/'){
        $route = $this->getRoute($this->root);
        $this->getCallback($route, []);
        Ampersand::getInstance()->run();
        $this->end();
      }
    }

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

    $robj = [];
    $route = $this->getPrefix($parameters[0]);
    $robj['method'] = $method;
    $robj['id'] = str_replace('=', '', base64_encode($method.$route));

    $robj['callback'] = $parameters[count($parameters) -1];
    $robj['middlewares'] = $this->middlewares;

    // Get the middlewares
    if(count($parameters) > 2) {
      for($i = 1; $i <= count($parameters) - 2; $i++){
        array_push($robj['middlewares'], $parameters[$i]);
      }
    }

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
    $this->flush();

  }

  private function getRoute($route_id) {
    foreach($this->routes as $route){
      if($route['id'] == $route_id)
      return $route;
    }
  }

  private function getUrlParameters($params, $query_vars) {
    $pars = [];
    foreach($params as $param){
      array_push($pars, $query_vars[$param]);
    }
    return $pars;
  }

  private function runCallback($cb, $query_vars, $res){

    if(is_object($cb) && ($cb instanceof Closure)){

      $res->bindAndCall($cb);

    } else if(is_string($mid)) {
      ob_start();
      call_user_func_array($cb, [$res->request, $res]);
      $res->write(ob_get_clean());
    }

  }

  private function getCallback($route, $query_vars){

    unset($query_vars['amp_route']);
    $req = new Request();
    $req->setVars($query_vars);
    $res = new Callback($req, $this->getUrlParameters($route['params'], $query_vars), $query_vars);

    if(count($route['middlewares']) > 0){
      foreach($route['middlewares'] as $mid){
        $this->runCallback($mid, $query_vars, $res);
      }
    }

    $this->runCallback($route["callback"], $query_vars, $res);

    Ampersand::getInstance()->setRequest($req);
    Ampersand::getInstance()->setResponse($res);

  }

  private function flush() {
    if($this->getEnv() == 'WP') $this->flush_rewrite_rules();
  }

  private function registerFilters() {
    if($this->getEnv() == 'WP') {
      add_filter('generate_rewrite_rules', [$this, 'rewrite_url']);
      add_filter('query_vars', [$this, 'query_vars']);
      add_filter('init',  [$this, 'flush_rewrite_rules']);
      add_action("parse_request", [$this, 'parse_request']);
    }
  }

  private function getEnv() {
    return defined('AMPERSAND_ENV') ? AMPERSAND_ENV : 'WP';
  }

  private function end(){
    if($this->getEnv() == 'WP')
      die(0);
  }


  // --- WordPress API

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
      $this->end();

    }
  }

}
