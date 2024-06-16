<?php
require '../helpers.php';
require basePath('Database.php');
require basePath('Router.php');

// Instantiate router before routes variable so it can be accessed from routes file
$router = new Router();
$routes = require basePath('routes.php');

// Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Route the request
$router->route($uri, $method);
