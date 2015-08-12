<?php

namespace Ampersand;
use Ampersand\Http\Response;

class Callback extends Response {

  public $request;
  public $parameters;

  public function __construct($req, $args) {
    $this->request = $req;
    $this->parameters = $args;
    parent::__construct();
  }

  public function params($key){
    return $this->request->params($key);
  }

  public function bindAndCall($cb){
    ob_start();
    $cb->bindTo($this, $this)->__invoke($this->parameters);
    $this->write(ob_get_clean());
  }

}
