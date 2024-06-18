<?php

// v1
// return [
//   '/' => 'controllers/home.php',
//   '/listings' => 'controllers/listings/index.php',
//   '/listings/create' => 'controllers/listings/create.php',
// ];

// v2
// $router->get('/', 'controllers/home.php');
// $router->get('/listings', 'controllers/listings/index.php');
// $router->get('/listings/create', 'controllers/listings/create.php');
// $router->get('/listing', 'controllers/listings/show.php');

// v3
$router->get('/', 'HomeController@index');
$router->get('/listings', 'ListingController@index');
$router->get('/listings/create', 'ListingController@create');
$router->get('/listings/{id}', 'ListingController@show');

$router->post('/listings/create', 'ListingController@store');
