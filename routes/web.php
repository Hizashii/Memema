<?php
/**
 * Web Routes
 */

$router->get('/', 'App\Http\Controllers\Front\HomeController@index');
$router->get('/movies', 'App\Http\Controllers\Front\MoviesController@index');
$router->get('/news', 'App\Http\Controllers\Front\NewsController@index');
$router->get('/venues', 'App\Http\Controllers\Front\VenuesController@index');
$router->get('/contact', 'App\Http\Controllers\Front\ContactController@index');
$router->post('/contact', 'App\Http\Controllers\Front\ContactController@submit');
$router->get('/booking', 'App\Http\Controllers\Front\BookingController@index');
$router->get('/booking/checkout', 'App\Http\Controllers\Front\BookingController@checkout');
$router->post('/booking/process', 'App\Http\Controllers\Front\BookingController@process');

$router->get('/login', 'App\Http\Controllers\Front\AuthController@loginForm');
$router->post('/login', 'App\Http\Controllers\Front\AuthController@login');
$router->get('/register', 'App\Http\Controllers\Front\AuthController@registerForm');
$router->post('/register', 'App\Http\Controllers\Front\AuthController@register');
$router->get('/logout', 'App\Http\Controllers\Front\AuthController@logout');
$router->get('/profile', 'App\Http\Controllers\Front\ProfileController@index');

