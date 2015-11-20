<?php

use Ampersand\Helpers\URL;

class HelperSpec extends PHPUnit_Framework_TestCase {

  function test_if_url_has_loaded() {
    $this->assertTrue(class_exists('URL'), 'Verify if the URL class has been loaded');
  }

  function test_if_url_has_method_to() {
    $this->assertTrue(method_exists('URL', 'to'), 'Verify if the to() method exists');
  }

  function test_if_to_returns_root() {
    $this->assertEquals('http://localhost', URL::to('/'));
  }

  function test_if_to_returns_path() {
    $this->assertEquals('http://localhost/hello', URL::to('/hello'));
  }

  function test_url_with_one_parameter() {
    $this->assertEquals('http://localhost/hello/you', URL::to('/hello/:name', 'you'));
  }

  function test_url_with_two_parameter() {
    $this->assertEquals('http://localhost/hello/Sergio/there/23', URL::to('/hello/:name/there/:age', 'Sergio', '23'));
  }

}
