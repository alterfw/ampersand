<?php

class Session {

  private static $instance;
  private $session;
  private $segment;

  private function __construct() {
    $session_factory = new \Aura\Session\SessionFactory;
    $this->session = $session_factory->newInstance($_COOKIE);
    $this->segment = $this->session->getSegment('Alter\Ampersand\Session');
  }

  public static function getInstance() {
    if(empty(self::$instance)) self::$instance = new Session();
    return self::$instance;
  }

  private function setFlash($key, $value) {
    $this->segment->setFlash($key, $value);
  }

  private function _get($key) {
    $val = $this->segment->get($key);
    if(empty($val)) $val = $this->segment->getFlash($key);
    return $val;
  }

  private function _set($key, $value) {
    $this->segment->set($key, $value);
  }

  private function _has($key) {
    $val = $this->_get($key);
    return !empty($val);
  }

  public static function flash($value, $key = 'message') {
    self::getInstance()->setFlash($key, $value);
  }

  public static function set($key, $value) {
    self::getInstance()->_set($key, $value);
  }

  public static function get($key) {
    return self::getInstance()->_get($key);
  }

  public static function has($key) {
    return self::getInstance()->_has($key);
  }

}
