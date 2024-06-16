<?php
require '../helpers.php';
require basePath('Database.php');
require basePath('Router.php');

// Instantiate router before routes variable so it can be accessed from routes file
$router = new Router();
$routes = require basePath('routes.php');

// Get current URI and HTTP method
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Route the request
$router->route($uri, $method);
