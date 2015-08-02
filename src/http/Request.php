<?php

namespace Ampersand\Http;

class Request extends \Slim\Http\Request {

  private $query_vars = [];

  public function __construct(){
    parent::__construct(\Slim\Environment::getInstance());
  }

  public function setVars($qr){
    $this->query_vars = $qr;
  }

  public function params($key = null, $default = null) {
    $union = array_merge($this->get(), $this->post(), $this->query_vars);
    if ($key) {
      return isset($union[$key]) ? $union[$key] : $default;
    }
    return $union;
  }


}
