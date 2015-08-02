<?php

use Ampersand\Http\Request;
use Ampersand\Http\Response;

class Config {

  public static function all(){
    $default_conf = require 'src/config.php';
    $user_config = __DIR__ . '/../../../config.php';
    $conf_user = [];
    if(file_exists($user_config)) $conf_user = require $user_config;
    $conf = array_merge($default_conf, $conf_user);
    return $conf;
  }

  public static function get($key) {
    $conf = self::all();
    return $conf[$key];
  }

}

class Ampersand {

  protected static $instance;
  private $request;
  private $response;
  private $settings;

  public static function getInstance($refresh = false) {
    if (is_null(self::$instance) || $refresh) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function __construct(){
    $this->settings = Config::all();
    $this->request = new Request();
    $this->response = new Response();
  }

  public function setRequest(\Ampersand\Http\Request $request) {
    $this->request = $request;
  }

  public function setResponse(\Ampersand\Http\Response $response) {
    $this->response = $response;
  }

  public function request(){
    return $this->request;
  }

  public function response(){
    return $this->response;
  }

  public function run() {
    //set_error_handler(array('\Slim\Slim', 'handleErrors'));

    //Fetch status, header, and body
    list($status, $headers, $body) = $this->response->finalize();

    // Serialize cookies (with optional encryption)
    \Slim\Http\Util::serializeCookies($headers, $this->response->cookies, $this->settings);

    //Send headers
    if (headers_sent() === false) {
      //Send status
      if (strpos(PHP_SAPI, 'cgi') === 0) {
        header(sprintf('Status: %s', \Slim\Http\Response::getMessageForCode($status)));
      } else {
        header(sprintf('HTTP/%s %s', Config::get('http.version'), \Slim\Http\Response::getMessageForCode($status)));
      }

      //Send headers
      foreach ($headers as $name => $value) {
        $hValues = explode("\n", $value);
        foreach ($hValues as $hVal) {
          header("$name: $hVal", false);
        }
      }
    }

    //Send body, but only if it isn't a HEAD request
    if (!$this->request->isHead()) {
      echo $body;
    }

    // restore_error_handler();
  }

}
