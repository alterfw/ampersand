<?php

class HelperSpec extends PHPUnit_Framework_TestCase {

  function test_if_url_has_loaded() {
    $this->assertTrue(class_exists('Ampersand\Helpers\URL'), 'Verify if the URL class has been loaded');
  }

  function test_if_url_has_method_to() {
    $this->assertTrue(method_exists('Ampersand\Helpers\URL', 'to'), 'Verify if the to() method exists');
  }

  function test_if_to_returns_root() {
    $this->assertEquals('http://localhost',  Ampersand\Helpers\URL::to('/'));
  }

  function test_if_to_returns_path() {
    $this->assertEquals('http://localhost/hello',  Ampersand\Helpers\URL::to('/hello'));
  }

  function test_url_with_one_parameter() {
    $this->assertEquals('http://localhost/hello/you',  Ampersand\Helpers\URL::to('/hello/:name', 'you'));
  }

  function test_url_with_two_parameter() {
    $this->assertEquals('http://localhost/hello/Sergio/there/23',  Ampersand\Helpers\URL::to('/hello/:name/there/:age', 'Sergio', '23'));
  }

}
