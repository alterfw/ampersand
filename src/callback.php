<?php

namespace Ampersand;
use Ampersand\Response;

class Callback extends Response {

  public $request;
  public $parameters;

  public function __construct($req, $args) {
    $this->request = $req;
    $this->parameters = $args;
  }

  public function params($key){
    return $this->request->params($key);
  }

}
