<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 9/21/15
 * Time: 1:37 AM
 */

namespace Ampersand;


class Collector {

  private $routeParser;
  private $dataGenerator;

  /**
   * Constructs a route collector.
   *
   * @param RouteParser   $routeParser
   * @param DataGenerator $dataGenerator
   */
  public function __construct(\FastRoute\RouteParser $routeParser, \FastRoute\DataGenerator $dataGenerator) {
    $this->routeParser = $routeParser;
    $this->dataGenerator = $dataGenerator;
  }

  public function addRoute($httpMethod, $route, $handler, $routeid) {
    $routeDatas = $this->routeParser->parse($route);
    foreach ((array) $httpMethod as $method) {
      foreach ($routeDatas as $routeData) {
        $this->dataGenerator->addRoute($method, $routeData, [$handler, $routeid]);
      }
    }
  }

  public function getData() {
    return $this->dataGenerator->getData();
  }

}