<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router
{
  protected $routes = [];

  /**
   * Add a new route
   *
   * @param string $method
   * @param string $uri
   * @param string $action
   * @param array $middleware
   * @return void
   */
  public function registerRoute($method, $uri, $action, $middleware = [])
  {
    list($controller, $controllerMethod) = explode('@', $action);

    $this->routes[] = [
      'method' => $method,
      'uri' => $uri,
      'controller' => $controller,
      'controllerMethod' => $controllerMethod,
      'middleware' => $middleware
    ];
  }

  /**
   * Add GET route
   * 
   * @param string $uri
   * @param string $controller
   * @param array $middleware
   * @return void
   */
  public function get($uri, $controller, $middleware = [])
  {
    $this->registerRoute('GET', $uri, $controller, $middleware);
  }
  /**
   * Add POST route
   * 
   * @param string $uri
   * @param string $controller
   * @param array $middleware
   * @return void
   */
  public function post($uri, $controller, $middleware = [])
  {
    $this->registerRoute('POST', $uri, $controller, $middleware);
  }
  /**
   * Add PUT route
   * 
   * @param string $uri
   * @param string $controller
   * @param array $middleware
   * @return void
   */
  public function put($uri, $controller, $middleware = [])
  {
    $this->registerRoute('PUT', $uri, $controller, $middleware);
  }
  /**
   * Add DELETE route
   * 
   * @param string $uri
   * @param string $controller
   * @param array $middleware
   * @return void
   */
  public function delete($uri, $controller, $middleware = [])
  {
    $this->registerRoute('DELETE', $uri, $controller, $middleware);
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
          foreach ($route['middleware'] as $role) {
            (new Authorize())->handle($role);
          }

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
