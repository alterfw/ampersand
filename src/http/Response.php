<?php

namespace Ampersand\Http;

class Response extends \Slim\Http\Response {

  public function render($template, $data = []) {
    $this->write(\Render::template($template, $data, false), true);
  }

  private function getTemplate($template){
    ob_start();
    require(get_query_template($template));
    return ob_get_clean();
  }

  public function template($template){
    $this->write($this->getTemplate($template));
  }

  public function toJSON($data){
    $this->headers = new \Slim\Http\Headers(array('Content-Type' => 'application/json'));
    $this->write(json_encode($data), true);
  }

}
