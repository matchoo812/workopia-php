<?php
require '../helpers.php';
require basePath('Router.php');

$router = new Router();
// make sure router is instantiated before routes variable so it can be accessed from routes file
$routes = require basePath('routes.php');

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method);
