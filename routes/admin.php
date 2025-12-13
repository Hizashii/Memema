<?php
/**
 * Admin Routes
 */

$router->get('/admin/login', 'App\Http\Controllers\Admin\AuthController@loginForm');
$router->post('/admin/login', 'App\Http\Controllers\Admin\AuthController@login');
$router->get('/admin/logout', 'App\Http\Controllers\Admin\AuthController@logout');
$router->get('/admin', 'App\Http\Controllers\Admin\DashboardController@index', ['App\Http\Middleware\AuthMiddleware']);

$router->get('/admin/movies', 'App\Http\Controllers\Admin\MoviesController@index', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/movies/create', 'App\Http\Controllers\Admin\MoviesController@create', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/movies', 'App\Http\Controllers\Admin\MoviesController@store', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/movies/edit', 'App\Http\Controllers\Admin\MoviesController@edit', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/movies/update', 'App\Http\Controllers\Admin\MoviesController@update', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/movies/delete', 'App\Http\Controllers\Admin\MoviesController@delete', ['App\Http\Middleware\AuthMiddleware']);

$router->get('/admin/news', 'App\Http\Controllers\Admin\NewsController@index', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/news/create', 'App\Http\Controllers\Admin\NewsController@create', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/news', 'App\Http\Controllers\Admin\NewsController@store', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/news/edit', 'App\Http\Controllers\Admin\NewsController@edit', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/news/update', 'App\Http\Controllers\Admin\NewsController@update', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/news/delete', 'App\Http\Controllers\Admin\NewsController@delete', ['App\Http\Middleware\AuthMiddleware']);

$router->get('/admin/venues', 'App\Http\Controllers\Admin\VenuesController@index', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/venues/create', 'App\Http\Controllers\Admin\VenuesController@create', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/venues', 'App\Http\Controllers\Admin\VenuesController@store', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/venues/edit', 'App\Http\Controllers\Admin\VenuesController@edit', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/venues/update', 'App\Http\Controllers\Admin\VenuesController@update', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/venues/delete', 'App\Http\Controllers\Admin\VenuesController@delete', ['App\Http\Middleware\AuthMiddleware']);

$router->get('/admin/bookings', 'App\Http\Controllers\Admin\BookingsController@index', ['App\Http\Middleware\AuthMiddleware']);

$router->get('/admin/users', 'App\Http\Controllers\Admin\UsersController@index', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/users/create', 'App\Http\Controllers\Admin\UsersController@create', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/users', 'App\Http\Controllers\Admin\UsersController@store', ['App\Http\Middleware\AuthMiddleware']);

$router->get('/admin/messages', 'App\Http\Controllers\Admin\MessagesController@index', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/messages/view', 'App\Http\Controllers\Admin\MessagesController@show', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/messages/delete', 'App\Http\Controllers\Admin\MessagesController@delete', ['App\Http\Middleware\AuthMiddleware']);

$router->get('/admin/bookings/invoice', 'App\Http\Controllers\Admin\BookingsController@invoice', ['App\Http\Middleware\AuthMiddleware']);

$router->get('/admin/settings', 'App\Http\Controllers\Admin\CompanySettingsController@index', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/settings', 'App\Http\Controllers\Admin\CompanySettingsController@update', ['App\Http\Middleware\AuthMiddleware']);

$router->get('/admin/contact-info', 'App\Http\Controllers\Admin\ContactInfoController@index', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/contact-info', 'App\Http\Controllers\Admin\ContactInfoController@update', ['App\Http\Middleware\AuthMiddleware']);

$router->get('/admin/shows', 'App\Http\Controllers\Admin\ShowsController@index', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/shows/create', 'App\Http\Controllers\Admin\ShowsController@create', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/shows', 'App\Http\Controllers\Admin\ShowsController@store', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/shows/edit', 'App\Http\Controllers\Admin\ShowsController@edit', ['App\Http\Middleware\AuthMiddleware']);
$router->post('/admin/shows/update', 'App\Http\Controllers\Admin\ShowsController@update', ['App\Http\Middleware\AuthMiddleware']);
$router->get('/admin/shows/delete', 'App\Http\Controllers\Admin\ShowsController@delete', ['App\Http\Middleware\AuthMiddleware']);

