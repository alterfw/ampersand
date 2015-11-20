<?php

namespace Ampersand\Http;
use Ampersand\Render;

class Response extends \Slim\Http\Response {

  public function render($template, $data = []) {
    $this->write(Render::view($template, $data));
  }

  public function template($template){
    $this->write(Render::template($template));
  }

  public function toJSON($data){
    $this->headers = new \Slim\Http\Headers(array('Content-Type' => 'application/json'));
    $this->write(json_encode($data), true);
  }

}
