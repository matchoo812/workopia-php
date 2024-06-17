<?php
require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

// automatically load framework classes
// spl_autoload_register(function ($class) {
//   $path = basePath('Framework/' . $class . '.php');
//   if (file_exists($path)) {
//     require $path;
//   }
// });

use Framework\Router;

// Instantiate router before routes variable so it can be accessed from routes file
$router = new Router();
$routes = require basePath('routes.php');

// Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Route the request
$router->route($uri, $method);
