<?php

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

}

class RouteImplementation {

  private $routes = [];

  public function __construct(){
    add_filter('generate_rewrite_rules', [$this, 'rewrite_url']);
    add_filter('query_vars', [$this, 'query_vars']);
    add_filter('init',  [$this, 'flush_rewrite_rules']);
    add_action("parse_request", [$this, 'parse_request']);
  }

  private function addRoute($method, $route, $callback) {

    $robj = [];

    $robj['method'] = $method;
    $robj['callback'] = $callback;
    $robj['id'] = str_replace('=', '', base64_encode($method.$route));

    // Generate the regex url
    $broken = explode('/', $route);
    if(count($broken) == 1) {
      $robj['regex'] = ''.$broken[0].'/?';
      $robj['qstring'] = 'index.php?amp_route='.$robj['id'];
      $robj['params'] = [];
    } else {

      $robj['regex'] = '';
      $robj['qstring'] = 'index.php?amp_route='.$robj['id'];
      $robj['params'] = [];
      $br = explode('/', $route);
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

    if(count($route['params'])  == 0){
      $route["callback"]();
    } else {
      $ev = '$route["callback"](';
      $par = [];
      foreach($route['params'] as $param){
        array_push($par, '$query_vars["'.$param.'"]');
      }
      $ev .= implode(',', $par);
      $ev .= ");";
      eval($ev);
    }

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
      if (!isset( $rules[$route['regex']])) $need = true;
    }

    if($need){
      global $wp_rewrite;
      $wp_rewrite->flush_rules();
    }

  }

  public function rewrite_url( $wp_rewrite ) {
    $new_rules = [];

    foreach($this->routes as $route) {
      $new_rules[$route['regex']] = $route['qstring'];
    }

    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    return $wp_rewrite->rules;
  }

  public function registerGET($route, $callback) {
    $this->addRoute('GET', $route, $callback);
  }

  public function parse_request($wp_query) {

    if (isset($wp_query->query_vars['amp_route'])){
      $route = $this->getRoute($wp_query->query_vars['amp_route']);
      if($route['id'] == $wp_query->query_vars['amp_route'] && $route['method'] == $_SERVER['REQUEST_METHOD']){
        $this->getCallback($route, $wp_query->query_vars);
        exit(0);
      } else {
        add_action( 'wp', 'force_404' );
        function force_404() {
          status_header( 404 );
          nocache_headers();
          include( get_query_template( '404' ) );
          die();
        }
      }

    }
  }

}
