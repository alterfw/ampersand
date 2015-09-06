<?php

class RouterSpec extends PHPUnit_Framework_TestCase {

  function setUP() {
    Route::reset();
  }

  function getCallback(){
    return function(){
      throw new Exception('Should never be called');
    };
  }

  function test_if_router_has_loaded() {
    $this->assertTrue(class_exists('Route'), 'Verify if the Router has been loaded');
  }

  // --- Methods

  function test_get_route() {

    Route::get('/hello', $this->getCallback());
    $target = Route::getRoutes()[0];
    $this->assertContains('GET', $target['methods']);
    $this->assertCount(1, $target['methods']);

  }

  function test_post_route() {

    Route::post('/hello', $this->getCallback());
    $target = Route::getRoutes()[0];
    $this->assertContains('POST', $target['methods']);

  }

  function test_put_route() {

    Route::put('/hello', $this->getCallback());
    $target = Route::getRoutes()[0];
    $this->assertContains('PUT', $target['methods']);

  }

  function test_delete_route() {

    Route::delete('/hello', $this->getCallback());

    $target = Route::getRoutes()[0];
    $this->assertContains('DELETE', $target['methods']);

  }

  function test_map_route() {

    Route::map(['GET', 'POST'], '/hello', $this->getCallback());
    $target = Route::getRoutes()[0];
    $this->assertContains('GET', $target['methods']);
    $this->assertContains('POST', $target['methods']);
    $this->assertCount(2, $target['methods']);

  }

  // --- Regex, parameters and query strings

  function test_root_route() {

    Route::get('/', $this->getCallback());
    $target = Route::getRoutes()[0];

    $this->assertCount(1, Route::getRoutes());
    $this->assertContains('GET', $target['methods']);
    $this->assertEquals($target['id'], str_replace('=', '', base64_encode('GET/')));
    $this->assertCount(0, $target['middlewares']);
    $this->assertCount(0, $target['params']);
    $this->assertFalse($target['regex']);
    $this->assertFalse($target['qstring']);

  }

  function test_first_level_route() {

    Route::get('/cars', $this->getCallback());
    $target = Route::getRoutes()[0];
    $id = str_replace('=', '', base64_encode('GET/cars'));

    $this->assertContains('GET', $target['methods']);
    $this->assertEquals($target['id'], $id);
    $this->assertCount(0, $target['middlewares']);
    $this->assertCount(0, $target['params']);
    $this->assertEquals($target['regex'], 'cars/?');
    $this->assertEquals($target['qstring'], 'index.php?amp_route='.$id);

  }

  function test_second_level_route() {

    Route::get('/my/cars', $this->getCallback());
    $target = Route::getRoutes()[0];
    $id = str_replace('=', '', base64_encode('GET/my/cars'));

    $this->assertContains('GET', $target['methods']);
    $this->assertEquals($target['id'], $id);
    $this->assertCount(0, $target['middlewares']);
    $this->assertCount(0, $target['params']);
    $this->assertEquals($target['regex'], 'my/cars/?');
    $this->assertEquals($target['qstring'], 'index.php?amp_route='.$id);

  }

  function test_route_with_one_parameter() {

    Route::get('/car/:model', $this->getCallback());
    $target = Route::getRoutes()[0];
    $id = str_replace('=', '', base64_encode('GET/car/:model'));

    $this->assertContains('GET', $target['methods']);
    $this->assertEquals($target['id'], $id);
    $this->assertCount(0, $target['middlewares']);
    $this->assertCount(1, $target['params']);
    $this->assertEquals($target['params'][0], 'model');
    $this->assertEquals($target['regex'], 'car/([a-zA-Z0-9-]+)/?');
    $this->assertEquals($target['qstring'], 'index.php?amp_route='.$id.'&model=$matches[1]');

  }

  function test_route_with_two_parameters() {

    Route::get('/bike/:model/:year', $this->getCallback());
    $target = Route::getRoutes()[0];
    $id = str_replace('=', '', base64_encode('GET/bike/:model/:year'));

    $this->assertContains('GET', $target['methods']);
    $this->assertEquals($target['id'], $id);
    $this->assertCount(0, $target['middlewares']);
    $this->assertCount(2, $target['params']);
    $this->assertEquals($target['params'][0], 'model');
    $this->assertEquals($target['params'][1], 'year');
    $this->assertEquals($target['regex'], 'bike/([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)/?');
    $this->assertEquals($target['qstring'], 'index.php?amp_route='.$id.'&model=$matches[1]&year=$matches[2]');

  }

  // --- Middlewares

  function test_route_with_one_middleware() {

    Route::get('/bike', 'mid1', $this->getCallback());
    $target = Route::getRoutes()[0];

    $this->assertCount(1, $target['middlewares']);
    $this->assertEquals($target['middlewares'][0], 'mid1');

  }

  function test_route_with_two_middlewares() {

    Route::get('/bike', 'mid1', 'mid2', $this->getCallback());
    $target = Route::getRoutes()[0];

    $this->assertCount(2, $target['middlewares']);
    $this->assertEquals($target['middlewares'][0], 'mid1');
    $this->assertEquals($target['middlewares'][1], 'mid2');

  }

  function test_route_with_closure_middleware() {

    Route::get('/bike', $this->getCallback(), $this->getCallback());
    $target = Route::getRoutes()[0];

    $this->assertCount(1, $target['middlewares']);
    $this->assertEquals(get_class($target['middlewares'][0]), 'Closure');
    $this->assertEquals($target['middlewares'][0], $this->getCallback());

  }

  // --- Group of routes

  function test_group_of_routes() {

    Route::group('admin', function(){
      Route::get('/dashboard', $this->getCallback());
    });

    $target = Route::getRoutes()[0];
    $id = str_replace('=', '', base64_encode('GET/admin/dashboard'));

    $this->assertEquals($target['regex'], 'admin/dashboard/?');
    $this->assertContains('GET', $target['methods']);
    $this->assertEquals($target['id'], $id);
    $this->assertCount(0, $target['middlewares']);
    $this->assertCount(0, $target['params']);

  }

  function test_group_of_routes_with_second_level_routes() {

    Route::group('admin', function(){
      Route::get('/panel/dashboard', $this->getCallback());
    });

    $target = Route::getRoutes()[0];
    $id = str_replace('=', '', base64_encode('GET/admin/panel/dashboard'));

    $this->assertEquals($target['regex'], 'admin/panel/dashboard/?');
    $this->assertContains('GET', $target['methods']);
    $this->assertEquals($target['id'], $id);
    $this->assertCount(0, $target['middlewares']);
    $this->assertCount(0, $target['params']);

  }

  function test_group_of_routes_with_second_level() {

    Route::group('admin/panel', function(){
      Route::get('/dashboard', $this->getCallback());
    });

    $target = Route::getRoutes()[0];
    $id = str_replace('=', '', base64_encode('GET/admin/panel/dashboard'));

    $this->assertEquals($target['regex'], 'admin/panel/dashboard/?');
    $this->assertContains('GET', $target['methods']);
    $this->assertEquals($target['id'], $id);
    $this->assertCount(0, $target['middlewares']);
    $this->assertCount(0, $target['params']);

  }

  function test_group_of_routes_with_parameters() {

    Route::group('admin', function(){
      Route::get('/dashboard/:section', $this->getCallback());
    });

    $target = Route::getRoutes()[0];
    $id = str_replace('=', '', base64_encode('GET/admin/dashboard/:section'));

    $this->assertEquals($target['regex'], 'admin/dashboard/([a-zA-Z0-9-]+)/?');
    $this->assertContains('GET', $target['methods']);
    $this->assertEquals($target['id'], $id);
    $this->assertCount(0, $target['middlewares']);
    $this->assertCount(1, $target['params']);
    $this->assertEquals($target['params'][0], 'section');

  }

  function test_group_of_routes_with_parameters_in_the_group() {

    Route::group('admin/:section', function(){
      Route::get('/dashboard', $this->getCallback());
    });

    $target = Route::getRoutes()[0];
    $id = str_replace('=', '', base64_encode('GET/admin/:section/dashboard'));

    $this->assertEquals($target['regex'], 'admin/([a-zA-Z0-9-]+)/dashboard/?');
    $this->assertContains('GET', $target['methods']);
    $this->assertEquals($target['id'], $id);
    $this->assertCount(0, $target['middlewares']);
    $this->assertCount(1, $target['params']);
    $this->assertEquals($target['params'][0], 'section');

  }

  function test_group_of_routes_with_one_middleware() {

    Route::group('admin', 'mid1', function(){
      Route::get('/dashboard', $this->getCallback());
    });

    $target = Route::getRoutes()[0];

    $this->assertCount(1, $target['middlewares']);
    $this->assertEquals($target['middlewares'][0], 'mid1');

  }

  function test_group_of_routes_with_two_middlewares() {

    Route::group('admin', 'mid1', 'mid2', function(){
      Route::get('/dashboard', $this->getCallback());
    });

    $target = Route::getRoutes()[0];

    $this->assertCount(2, $target['middlewares']);
    $this->assertEquals($target['middlewares'][0], 'mid1');
    $this->assertEquals($target['middlewares'][1], 'mid2');

  }

}
