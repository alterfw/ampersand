<?php

class SessionSpec extends PHPUnit_Framework_TestCase {

  function test_if_session_has_loaded() {
    $this->assertTrue(class_exists('Session'), 'Verify if the Session class has been loaded');
  }

  function test_if_session_has_flash_method() {
    $this->assertTrue(method_exists('Session', 'flash'), 'Tests if flash() method exists');
  }

  function test_if_session_has_get_method() {
    $this->assertTrue(method_exists('Session', 'get'), 'Tests if get() method exists');
  }

  function test_if_session_has_set_method() {
    $this->assertTrue(method_exists('Session', 'set'), 'Tests if set() method exists');
  }

  function test_if_session_has_has_method() {
    $this->assertTrue(method_exists('Session', 'has'), 'Tests if has() method exists');
  }

  function test_if_session_persist() {
    Session::set('test', 'hello');
    $this->assertEquals('hello', Session::get('test'));
  }

  function test_if_has_works() {
    Session::set('to_be', 'Or no to be');
    $this->assertTrue(Session::has('to_be'));
  }

  function test_without_static() {
    Session::getInstance()->set('hello', 'world');
    $this->assertEquals('world', Session::getInstance()->get('hello'));
  }

  function test_if_flash_message_is_persisted() {
    Session::flash('test!');
    $this->assertEquals('test!', $_SESSION['Aura\Session\Flash\Next']['Alter\Ampersand\Session']['message'], 'Test if the flash message was persisted in session');
  }

  function test_if_flash_error_is_persisted() {
    Session::flash('error!', 'error');
    $this->assertEquals('error!', $_SESSION['Aura\Session\Flash\Next']['Alter\Ampersand\Session']['error'], 'Test if the flash error was persisted in session');
  }

}