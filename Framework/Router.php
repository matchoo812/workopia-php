<?php

namespace Framework;

use App\Controllers\ErrorController;

class Router
{
  protected $routes = [];

  /**
   * Add a new route
   *
   * @param string $method
   * @param string $uri
   * @param string $action
   * @return void
   */
  public function registerRoute($method, $uri, $action)
  {
    list($controller, $controllerMethod) = explode('@', $action);

    $this->routes[] = [
      'method' => $method,
      'uri' => $uri,
      'controller' => $controller,
      'controllerMethod' => $controllerMethod
    ];
  }

  /**
   * Add GET route
   * 
   * @param string $uri
   * @param string $controller
   * @return void
   */
  public function get($uri, $controller)
  {
    $this->registerRoute('GET', $uri, $controller);
  }
  /**
   * Add POST route
   * 
   * @param string $uri
   * @param string $controller
   * @return void
   */
  public function post($uri, $controller)
  {
    $this->registerRoute('POST', $uri, $controller);
  }
  /**
   * Add PUT route
   * 
   * @param string $uri
   * @param string $controller
   * @return void
   */
  public function put($uri, $controller)
  {
    $this->registerRoute('PUT', $uri, $controller);
  }
  /**
   * Add DELETE route
   * 
   * @param string $uri
   * @param string $controller
   * @return void
   */
  public function delete($uri, $controller)
  {
    $this->registerRoute('DELETE', $uri, $controller);
  }

  /**
   * Route the request
   * 
   * @param string $uri
   * @param string $method
   * @return void 
   */
  public function route($uri)
  {
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    // check for method input to create delete request
    if ($requestMethod === 'POST' && isset($_POST['_method'])) {
      // override request method with hidden input method
      $requestMethod = strtoupper($_POST['_method']);
    }

    foreach ($this->routes as $route) {
      // split current uri
      $uriSegments = explode('/', trim($uri, '/'));
      // split route uri into segments
      $routeSegments = explode('/', trim($route['uri'], '/'));

      $match = true;

      // check if number of segments matches
      if (count($uriSegments) === count($routeSegments) && strtoupper($route['method'] === $requestMethod)) {
        $params = [];

        $match = true;

        for ($i = 0; $i < count($uriSegments); $i++) {
          // check if uri's don't match and there are no params
          if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
            $match = false;
            break;
          }

          // check for param and add to $params array
          if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
            // set key-value pair using matches value (from routes) and uri segment (value passed in from url)
            $params[$matches[1]] = $uriSegments[$i];
            // inspectAndDie($params);
          }
        }

        if ($match) {
          $controller = 'App\\Controllers\\' . $route['controller'];
          $controllerMethod = $route['controllerMethod'];

          // instantiate controller and call method
          $controllerInstance = new $controller();
          $controllerInstance->$controllerMethod($params);
          return;
        }
      }
    }

    // throw an error if the specified route is not found
    ErrorController::notFound();
  }
}
