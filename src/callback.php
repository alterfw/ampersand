<?php

namespace Ampersand;
use Ampersand\Http\Response;

class Callback extends Response {

  public $request;
  public $parameters;
  private $args;

  public function __construct($req, $args, $parameters) {
    $this->request = $req;
    $this->args = $args;
    $this->parameters = $parameters;
    parent::__construct();
  }

  public function params($key){
    return $this->request->params($key);
  }

  public function bindAndCall($cb){
    ob_start();
    $cb->bindTo($this, $this)->__invoke($this->args);
    $this->write(ob_get_clean());
  }

}
